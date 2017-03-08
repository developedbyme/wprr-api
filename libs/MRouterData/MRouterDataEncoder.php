<?php
	namespace MRouterData;

	use \WP_Query;
	use \WP_Term;
	use \WP_Post;
	use \WP_User;

	// \MRouterData\MRouterDataEncoder
	class MRouterDataEncoder {
		
		protected $_performance = array();
		
		function __construct() {
			
		}
		
		protected function _add_performance_data($type, $value) {
			if(!isset($this->_performance[$type])) {
				$this->_performance[$type] = array();
			}
			$this->_performance[$type][] = $value;
		}
		
		public function get_performance_data() {
			return $this->_performance;
		}

		protected function _get_meta_data() {
			$returnObject = array();

			$returnObject['mRouter'] = array('version' => M_ROUTER_DATA_VERSION);

			global $wp_version;
			$returnObject['wordpress'] = array('version' => $wp_version);

			return $returnObject;
		}

		public function encode_post($post) {
			//echo('encode_post');
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

			$current_post_data["parent"] = $this->encode_post_link($post->post_parent);

			$children_ids = get_posts(array(
				'post_type' => 'any',
				'post_parent' => $post_id,
				'posts_per_page' => -1,
				'orderby' => 'menu_order title',
				'order' => 'ASC',
				'fields' => 'ids'
			));

			$children = array();
			foreach($children_ids as $child_id) {
				$children[] = $this->encode_post_link($child_id);
			}
			$current_post_data["children"] = $children;

			$media_post_id = get_post_thumbnail_id($post_id);
			if($media_post_id) {
				$media_post = get_post($media_post_id);

				$current_post_data["image"] = $this->encode_image($media_post);
			}
			else {
				$current_post_data["image"] = null;
			}

			$author_id = $post->post_author;
			$author = get_user_by('ID', $author_id);
			$current_post_data["author"] = $this->encode_user($author);

			$current_post_data["meta"] = get_post_meta($post_id);

			$current_post_data["acf"] = null;
			$fields_object = get_field_objects($post_id);
			if($fields_object !== false) {

				$acf_object = array();
				foreach($fields_object as $name => $field_object) {
					$acf_object[$name] = $this->encode_acf_field($field_object, $post_id);
				}

				$current_post_data["acf"] = $acf_object;
			}

			$current_post_data["languages"] = null;
			if ( function_exists('icl_get_languages') ) {
        $current_post_data["languages"] = icl_get_languages( "skip_missing=0" );
				$current_post_data["languages"]["ICL_LANGUAGE_CODE"] = ( ICL_LANGUAGE_CODE == "sv" ? "en" : "sv" );
			}

			$taxonomies = array_keys(get_the_taxonomies($post_id));
			$term_data_array = array();
			foreach($taxonomies as $taxonomy) {

				$current_taxonomy_data = array();
				$terms = get_the_terms($post_id, $taxonomy);
				foreach($terms as $term) {
					$current_taxonomy_data[] = $this->encode_term($term);
				}

				$term_data_array[$taxonomy] = $current_taxonomy_data;
			}
			$current_post_data["terms"] = $term_data_array;

			return $current_post_data;
		}

		public function encode_image($media_post) {
			//var_dump($media_post);
			
			$start_time = microtime(true);

			$media_post_id = $media_post->ID;
			$media_meta = get_post_meta($media_post_id, '_wp_attachment_metadata', true);
			$sizes = $media_meta['sizes'];
			
			$img_url = wp_get_attachment_url($media_post_id);
			$img_url_basename = wp_basename($img_url);

			$image_data = array();

			$image_data['id'] = $media_post_id;
			$image_data['title'] = get_the_title($media_post_id);
			$image_data['permalink'] = get_permalink($media_post_id);

			$image_data['alt'] = get_post_meta($media_post_id, '_wp_attachment_image_alt', true);
			$image_data['caption'] = $media_post->post_excerpt;
			$image_data['description'] = $media_post->post_content;
			
			$image_size_data = array();
			if(is_array($sizes)) {
				foreach($sizes as $size_name => $size_data) {
					
					$current_url = str_replace( $img_url_basename, $size_data['file'], $img_url );
					
					$image_size_data[$size_name] = array('url' => $current_url, 'width' => $size_data['width'], 'height' =>  $size_data['height']);
				}
			}
			
			$image_size_data['full'] = array('url' => $img_url, 'width' => $media_meta['width'], 'height' => $media_meta['height']);

			$image_data["sizes"] = $image_size_data;
			
			$end_time = microtime(true);
			$this->_add_performance_data('encode_image', $end_time-$start_time);

			return $image_data;
		}

		public function encode_file($media_post) {

			$media_post_id = $media_post->ID;

			$image_data = array();

			$image_data['id'] = $media_post_id;
			$image_data['title'] = get_the_title($media_post_id);

			$image_data['alt'] = get_post_meta($media_post_id, '_wp_attachment_image_alt', true);
			$image_data['caption'] = $media_post->post_excerpt;
			$image_data['description'] = $media_post->post_content;

			$image_data['url'] = wp_get_attachment_url($media_post_id);

			$preview_url = wp_get_attachment_thumb_url($media_post_id);

			$image_data['previewUrl'] = $preview_url ? $preview_url : null;
			$image_data['mimeType'] = $media_post->post_mime_type;
			
			

			return $image_data;
		}

		protected function _encode_acf_single_post_object_or_id($post_or_id) {
			if($post_or_id instanceof \WP_Post) {
				return $this->encode_post_link($post_or_id->ID);
			}
			else {
				return $this->encode_post_link($post_or_id);
			}
		}

		protected function _encode_acf_post_object($value) {
			if($value === false) {
				return null;
			}

			$return_array = array();

			if(is_array($value)) {

				foreach($value as $post_or_id) {
					$return_array[] = $this->_encode_acf_single_post_object_or_id($post_or_id);
				}
			}
			else {
				$return_array[] = $this->_encode_acf_single_post_object_or_id($value);
			}

			return $return_array;
		}

		protected function _encode_acf_single_image_or_id($post_or_id) {
			if($post_or_id instanceof \WP_Post) {
				return $this->encode_image($post_or_id);
			}
			else {
				return $this->encode_image(get_post($post_or_id));
			}
		}

		protected function _encode_acf_image($value) {

			if($value === false || $value === null) {
				return null;
			}

			$return_array = array();

			if(is_array($value)) {

				foreach($value as $post_or_id) {
					$return_array[] = $this->_encode_acf_single_image_or_id($post_or_id);
				}
			}
			else {
				$return_array[] = $this->_encode_acf_single_image_or_id($value);
			}

			return $return_array;
		}

		protected function _encode_acf_single_taxonomy_or_id($term_or_id, $taxonomy) {
			if($term_or_id instanceof \WP_Term) {
				return $this->encode_term($term_or_id);
			}
			else {
				return $this->encode_term(get_term_by('id', $term_or_id, $taxonomy));
			}
		}

		protected function _encode_acf_taxonomy($value, $taxonomy) {

			if($value === false || $value === null) {
				return null;
			}

			$return_array = array();

			if(is_array($value)) {

				foreach($value as $id) {
					$return_array[] = $this->_encode_acf_single_taxonomy_or_id($id, $taxonomy);
				}
			}
			else {
				$return_array[] = $this->_encode_acf_single_taxonomy_or_id($value, $taxonomy);
			}

			return $return_array;
		}

		public function encode_acf_field($field, $post_id, $override_value = null) {
			//echo('encode_acf_field');
			//var_dump($field);

			$return_object = array();

			$type = $field['type'];
			$return_object['type'] = $type;

			$field_value = $override_value ? $override_value : $field['value'];

			switch($type) {
				case 'repeater':
					$rows_array = array();
					$current_key = $field['key'];
					if(have_rows($current_key, $post_id)) {
						while(have_rows($current_key, $post_id)) {

							the_row();
							$current_row = get_row();

							$row_result = array();

							foreach($current_row as $key => $value) {
								$current_row_field = get_field_object($key, $post_id, false, true);
								$row_result[$current_row_field['name']] = $this->encode_acf_field($current_row_field, $post_id, $value);
							}

							array_push($rows_array, $row_result);
						}
					}

					$return_object['value'] = $rows_array;
					break;
				case 'image':
					if(is_array($field_value)) {
						$return_object['value'] = $this->_encode_acf_image($field_value['id']);
					}
					else {
						$return_object['value'] = $this->_encode_acf_image($field_value);
					}
					break;
				case 'gallery':
					$return_object['value'] = $this->_encode_acf_image($field_value);
					break;
				case 'file':
					if($field_value) {
						if(is_array($field_value)) {
							$return_object['value'] = $this->encode_file(get_post($field_value['id']));
						}
						else {
							$return_object['value'] = $this->encode_file(get_post($field_value));
						}
					}
					else {
						$return_object['value'] = null;
					}
					break;
				case 'post_object':
				case 'relationship':
					$return_object['value'] = $this->_encode_acf_post_object($field_value);
					break;
				case 'taxonomy': //METODO: implement this
					$taxonomy = $field['taxonomy'];
					$return_object['value'] = $this->_encode_acf_taxonomy($field_value, $taxonomy);
					break;
				case "oembed":
					if($field_value) {
						$return_object['value'] = array('url' => $field_value, 'oembed' => wp_oembed_get($field_value));
					}
					else {
						$return_object['value'] = null;
					}
					break;
				default:
					$return_object['value'] = $field_value;
					break;
			}

			return $return_object;
		}

		public function encode_post_link($post_id) {
			//echo('encode_post_link');
			//var_dump($post_id);

			if($post_id === 0 || get_post_status($post_id) !== "publish") {
				return null;
			}

			$current_post_data["id"] = $post_id;
			$current_post_data["permalink"] = get_permalink($post_id);
			$current_post_data["title"] = get_the_title($post_id);

			return $current_post_data;
		}

		public function encode_term($term) {

			$return_object = array();

			$return_object['id'] = $term->term_id;
			$return_object['permalink'] = get_term_link($term);
			$return_object['name'] = $term->name;
			$return_object['slug'] = $term->slug;
			$return_object['description'] = $term->description;
			$return_object['taxonomy'] = $term->taxonomy;
			//METODO: add taxonomy name
			
			$return_object["meta"] = get_term_meta($term->term_id);

			return $return_object;
		}

		public function encode_user($user) {

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

		public function encode() {
			ob_start();

			try {

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
					$data['data']['queriedData'] = $this->encode_post($queried_object);
				}
				else if($queried_object instanceof \WP_Term) {
					$data['data']['type'] = 'term';
					$data['data']['queriedData'] = $this->encode_term($queried_object);
				}
				else if($queried_object instanceof \WP_User) {
					$data['data']['type'] = 'user';
					$data['data']['queriedData'] = $this->encode_user($queried_object);
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

					$posts[] = $this->encode_post(get_post());
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
				
				$query_data = array();
				
				$query_data['searchQuery'] = ($wp_query->is_search ? get_search_query() : null);
				$query_data['numberOfPosts'] = intval($wp_query->found_posts);
				$query_data['numberOfPaginationPages'] = intval($wp_query->max_num_pages);
				
				if($query_data['numberOfPaginationPages'] === 0) {
					$query_data['currentPaginationIndex'] = 0;
				}
				else {
					$query_data['currentPaginationIndex'] = max(1, get_query_var('paged', 1));
				}
				
				
				
				$data['data']['queryData'] = $query_data;

			}
			catch(Exception $error) {
				var_dump($error);
				$data['metadata']['phpError'] = $error;
			}

			$php_output = ob_get_contents();
			ob_clean();

			$data['metadata']['phpOutput'] = $php_output;

			return $data;
		}

		public static function test_import() {
			echo("Imported \MRouterData\MRouterDataEncoder<br />");
		}
	}
?>
