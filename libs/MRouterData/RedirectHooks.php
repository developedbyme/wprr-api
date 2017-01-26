<?php
	namespace MRouterData;
	
	use \WP_Query;
	use \WP_Term;
	use \WP_Post;
	use \WP_User;
	
	// \MRouterData\RedirectHooks
	class RedirectHooks {
		
		protected $settings = null;
		
		function __construct() {
			//echo("\MRouterData\RedirectHooks::__construct<br />");
			
			
		}
		
		public function register() {
			//echo("\MRouterData\RedirectHooks::register<br />");
			
			add_action('template_redirect', array($this, 'hook_template_redirect'));
			
		}
		
		
		
		public function hook_template_redirect() {
			//echo("\MRouterData\RedirectHooks::hook_template_redirect<br />");
			
			if(isset($_GET['mRouterData']) && $_GET['mRouterData'] === 'json') {
			
				$debug = false;
			
				global $wp_query;
			
				$template_selection_parameters = array("is_single", "is_preview", "is_page", "is_archive", "is_date", "is_year", "is_month", "is_day", "is_time", "is_author", "is_category", "is_tag", "is_tax", "is_search", "is_feed", "is_comment_feed", "is_trackback", "is_home", "is_404", "is_embed", "is_paged", "is_admin", "is_attachment", "is_singular", "is_robots", "is_posts_page", "is_post_type_archive");
			
				$data = array();
				$data['metadata'] = array();
				$data['data'] = array();
			
				$data['metadata']['mRouter'] = array('version' => M_ROUTER_DATA_VERSION);
			
				$queried_object = get_queried_object();
			
				if($debug) {
					$data['data']['_queried_object'] = $queried_object;
					$data['data']['_query'] = $wp_query;
				}
				
				if($queried_object instanceof \WP_Post) {
					$data['data']['type'] = 'post';
					$data['data']['queriedData'] = $this->_encode_post($queried_object);
				}
				else if($queried_object instanceof \WP_Term) {
					$data['data']['type'] = 'term';
					$data['data']['queriedData'] = $this->_encode_term($queried_object);
				}
				else if($queried_object instanceof \WP_User) {
					$data['data']['type'] = 'user';
					$data['data']['queriedData'] = $this->_encode_user($queried_object);
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
					
					$posts[] = $this->_encode_post(get_the_ID());
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
			
			$current_post_data = array();
			
			$post = get_post();
			$post_id = $post->ID;
		
			$current_post_data["id"] = $post_id;
			$current_post_data["type"] = $post->post_type;
			$current_post_data["status"] = $post->post_status;
			$current_post_data["permalink"] = get_permalink($post_id);
			$current_post_data["publishedDate"] = $post->post_date;
			$current_post_data["modifiedDate"] = $post->post_modified;
			$current_post_data["title"] = get_the_title($post_id);
			$current_post_data["excerpt"] = apply_filters('the_excerpt', get_the_excerpt($post_id));
			$current_post_data["content"] = apply_filters('the_content', get_the_content($post_id));
			
			$author_id = $post->post_author;
			$author = get_user_by('ID', $author_id);
			$current_post_data["author"] = $this->_encode_user($author);
			
			$current_post_data["meta"] = get_post_meta($post_id);
			
			//METODO: add acf fields
		
			$taxonomies = array_keys(get_the_taxonomies($post_id));
			$term_data_array = array();
			foreach($taxonomies as $taxonomy) {
				
				$current_taxonomy_data = array();
				$terms = get_the_terms($post_id, $taxonomy);
				foreach($terms as $term) {
					$current_taxonomy_data[] = $this->_encode_term($term);
				}
				
				$term_data_array[$taxonomy] = $current_taxonomy_data;
			}
			$current_post_data["terms"] = $term_data_array;
		
			return $current_post_data;
		}
		
		protected function _encode_term($term) {
			
			$return_object = array();
			
			$return_object['id'] = $term->term_id;
			$return_object['link'] = get_term_link($term);
			$return_object['name'] = $term->name;
			$return_object['description'] = $term->description;
			
			return $return_object;
		}
		
		protected function _encode_user($user) {
			
			if(!$user) {
				return null;
			}
			
			$return_object = array();
			
			$return_object['id'] = $user->ID;
			$return_object['link'] = get_author_posts_url($user->ID);
			$return_object['name'] = $user->display_name;
			
			return $return_object;
		}
		
		public static function test_import() {
			echo("Imported \MRouterData\RedirectHooks<br />");
		}
	}
?>