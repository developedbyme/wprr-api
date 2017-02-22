<?php
	namespace MRouterData;

	use \WP_Query;
	use \WP_Term;
	use \WP_Post;
	use \WP_User;

	// \MRouterData\RedirectHooks
	class RedirectHooks {

		protected $settings = null;
		
		protected $encoder = null;

		function __construct() {
			//echo("\MRouterData\RedirectHooks::__construct<br />");
			
			$this->encoder = new \MRouterData\MRouterDataEncoder();

			// ACF go thrught the output data
			add_filter('acf/load_value', function( $value, $post_id, $field ) {
	    	return $value;
			}, 10, 3);
		}

		public function register() {
			//echo("\MRouterData\RedirectHooks::register<br />");

			add_action('template_redirect', array($this, 'hook_template_redirect'));

		}

		protected function _get_meta_data() {
			$returnObject = array();

			$returnObject['mRouter'] = array('version' => M_ROUTER_DATA_VERSION);

			global $wp_version;
			$returnObject['wordpress'] = array('version' => $wp_version);

			return $returnObject;
		}

		public function hook_template_redirect() {
			//echo("\MRouterData\RedirectHooks::hook_template_redirect<br />");

			if(isset($_GET['mRouterData']) && $_GET['mRouterData'] === 'json') {

				$debug = false;

				global $wp_query;

				$template_selection_parameters = array("is_single", "is_preview", "is_page", "is_archive", "is_date", "is_year", "is_month", "is_day", "is_time", "is_author", "is_category", "is_tag", "is_tax", "is_search", "is_feed", "is_comment_feed", "is_trackback", "is_home", "is_404", "is_embed", "is_paged", "is_admin", "is_attachment", "is_singular", "is_robots", "is_posts_page", "is_post_type_archive");

				$data = array();
				$data['data'] = array();

				$data['metadata'] = $this->_get_meta_data();

				$queried_object = get_queried_object();

				if($debug) {
					$data['data']['_queried_object'] = $queried_object;
					$data['data']['_query'] = $wp_query;
				}

				if($queried_object instanceof \WP_Post) {
					$data['data']['type'] = 'post';
					$data['data']['queriedData'] = $this->encoder->encode_post($queried_object);
				}
				else if($queried_object instanceof \WP_Term) {
					$data['data']['type'] = 'term';
					$data['data']['queriedData'] = $this->encoder->encode_term($queried_object);
				}
				else if($queried_object instanceof \WP_User) {
					$data['data']['type'] = 'user';
					$data['data']['queriedData'] = $this->encoder->encode_user($queried_object);
				}
				else if($queried_object === null) {
					$data['data']['type'] = 'none';
					$data['data']['queriedData'] = null;
				}
				else {
					$data['data']['type'] = 'unknown';
					$data['data']['queriedData'] = null;
				}

				$posts = array();

				while(have_posts()) {
					the_post();

					$posts[] = $this->encoder->encode_post(get_post());
				}

				$data['data']['posts'] = $posts;

				$template_selection = array();

				foreach($template_selection_parameters as $template_selection_parameter) {
					$template_selection[$template_selection_parameter] = $wp_query->$template_selection_parameter;
				}

				$template_selection['is_front_page'] = is_front_page();

				$template_selection['post_type'] = ($queried_object instanceof \WP_Post) ? $queried_object->post_type : null;
				$template_selection['taxonomy'] = ($queried_object instanceof \WP_Term) ? $queried_object->taxonomy : null;

				$data['data']['templateSelection'] = $template_selection;

				header('Content-Type: application/json');
				header("Access-Control-Allow-Origin: *");
				echo(json_encode($data));

				exit();
			}
		}

		protected function _encode_post($post) {
			//echo('_encode_post');
			//var_dump($post);

			$current_post_data = array();

			$post_id = $post->ID;

			$current_post_data["id"] = $post_id;
			$current_post_data["type"] = $post->post_type;
			$current_post_data["status"] = $post->post_status;
			$current_post_data["permalink"] = get_permalink($post_id);
			$current_post_data["publishedDate"] = $post->post_date;
			$current_post_data["modifiedDate"] = $post->post_modified;
			$current_post_data["title"] = get_the_title($post_id);
			$current_post_data["excerpt"] = apply_filters('the_excerpt', $post->post_excerpt);
			$current_post_data["content"] = apply_filters('the_content', $post->post_content);

			$media_post_id = get_post_thumbnail_id($post_id);
			if($media_post_id) {
				$media_post = get_post($media_post_id);
				$media_meta = get_post_meta($media_post_id, '_wp_attachment_metadata', true);
				$sizes = $media_meta['sizes'];

				$image_data = array();

				$image_data['id'] = $media_post_id;
				$image_data['title'] = get_the_title($media_post_id);
				$image_data['permalink'] = get_permalink($media_post_id);


				$image_size_data = array();
				foreach($sizes as $size_name => $size_data) {
					$image_url_and_size = wp_get_attachment_image_src($media_post_id, $size_name);
					$image_size_data[$size_name] = array('url' => $image_url_and_size[0], 'width' => $image_url_and_size[1], 'height' => $image_url_and_size[2]);
				}
				$image_url_and_size = wp_get_attachment_image_src($media_post_id, 'full');
				$image_size_data['full'] = array('url' => $image_url_and_size[0], 'width' => $image_url_and_size[1], 'height' => $image_url_and_size[2]);

				$image_data["sizes"] = $image_size_data;

				$current_post_data["image"] = $image_data;
			}
			else {
				$current_post_data["image"] = null;
			}

			$author_id = $post->post_author;
			$author = get_user_by('ID', $author_id);
			$current_post_data["author"] = $this->encoder->encode_user($author);

			$current_post_data["meta"] = get_post_meta($post_id);
			$current_post_data["acf"] = get_field_objects($post_id);

			$taxonomies = array_keys(get_the_taxonomies($post_id));
			$term_data_array = array();
			foreach($taxonomies as $taxonomy) {

				$current_taxonomy_data = array();
				$terms = get_the_terms($post_id, $taxonomy);
				foreach($terms as $term) {
					$current_taxonomy_data[] = $this->encoder->encode_term($term);
				}

				$term_data_array[$taxonomy] = $current_taxonomy_data;
			}
			$current_post_data["terms"] = $term_data_array;

			return $current_post_data;
		}

		protected function _encode_term($term) {

			$return_object = array();

			$return_object['id'] = $term->term_id;
			$return_object['permalink'] = get_term_link($term);
			$return_object['name'] = $term->name;
			$return_object['description'] = $term->description;
			$return_object['taxonomy'] = $term->taxonomy;
			//METODO: add taxonomy name

			return $return_object;
		}

		protected function _encode_user($user) {

			if(!$user) {
				return null;
			}

			$return_object = array();

			$return_object['id'] = $user->ID;
			$return_object['permalink'] = get_author_posts_url($user->ID);
			$return_object['name'] = $user->display_name;

			$return_object['gravatarHash'] = md5( strtolower( trim( $user->email ) ) );

			return $return_object;
		}

		public static function test_import() {
			echo("Imported \MRouterData\RedirectHooks<br />");
		}
	}
?>
