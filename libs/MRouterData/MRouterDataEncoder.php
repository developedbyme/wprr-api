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

			$start_time = microtime(true);

			$current_post_data = array();

			$post_id = $post->ID;
			
			do_action('m_router_data/prepare_post_encoding', $post_id, $post);

			$start_time_part = microtime(true);

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
			
			$comments_arguments = array(
				'post_id' => $post_id,
				'count' => true
			);
			$current_post_data["numberOfComments"] = get_comments($comments_arguments);

			$end_time_part = microtime(true);
			$this->_add_performance_data('encode_post/basic_data', $end_time_part-$start_time_part);

			$start_time_part = microtime(true);

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

			$end_time_part = microtime(true);
			$this->_add_performance_data('encode_post/children', $end_time_part-$start_time_part);

			$start_time_part = microtime(true);

			$media_post_id = get_post_thumbnail_id($post_id);
			if($media_post_id) {
				$media_post = get_post($media_post_id);

				$current_post_data["image"] = $this->encode_image($media_post);
			}
			else if($post->post_type === 'attachment') {
				$current_post_data["image"] = $this->encode_image($post);
			}
			else {
				$current_post_data["image"] = null;
			}

			$end_time_part = microtime(true);
			$this->_add_performance_data('encode_post/image', $end_time_part-$start_time_part);

			$start_time_part = microtime(true);

			$author_id = $post->post_author;
			$author = get_user_by('ID', $author_id);
			$current_post_data["author"] = $this->encode_user($author);

			$end_time_part = microtime(true);
			$this->_add_performance_data('encode_post/author', $end_time_part-$start_time_part);

			$start_time_part = microtime(true);
			
			$encoded_meta_data = array();
			$meta_data = get_post_meta($post_id);
			foreach($meta_data as $key => $value) {
				$encoded_meta_data[$key] = get_post_meta($post_id, $key, false);
			}

			$current_post_data["meta"] = apply_filters('m_router_data/filter_post_meta', $encoded_meta_data, $post_id);

			$end_time_part = microtime(true);
			$this->_add_performance_data('encode_post/meta', $end_time_part-$start_time_part);

			$start_time_part = microtime(true);

			if(function_exists('get_field_objects')) {
				$start_time_acf_part = microtime(true);

				$current_post_data["acf"] = null;
				$fields_object = get_field_objects($post_id, false, true); //get_field_objects($post_id); //

				$end_time_acf_part = microtime(true);
				$this->_add_performance_data('encode_post/acf/get_field_objects', $end_time_acf_part-$start_time_acf_part);

				if($fields_object !== false) {

					$start_time_acf_part = microtime(true);

					$acf_object = array();
					foreach($fields_object as $name => $field_object) {
						$acf_object[$name] = $this->encode_acf_field($field_object, $post_id);
					}

					$end_time_acf_part = microtime(true);
					$this->_add_performance_data('encode_post/acf/encode', $end_time_acf_part-$start_time_acf_part);

					$current_post_data["acf"] = $acf_object;
				}

				$end_time_part = microtime(true);
				$this->_add_performance_data('encode_post/acf', $end_time_part-$start_time_part);
			}
			
			
			$start_time_part = microtime(true);
			
			$add_ons = apply_filters('m_router_data/encode_post_add_ons', array(), $post_id, $post, $this);
			
			$current_post_data["addOns"] = $add_ons;
			
			$end_time_part = microtime(true);
			$this->_add_performance_data('encode_post/encode_post_add_ons', $end_time_part-$start_time_part);

			$start_time_part = microtime(true);

			$current_post_data["languages"] = null;
			if ( function_exists('icl_get_languages') ) {
				$current_post_data["languages"] = icl_get_languages( "skip_missing=0" );
				$current_post_data["languages"]["ICL_LANGUAGE_CODE"] = ( ICL_LANGUAGE_CODE == "sv" ? "en" : "sv" );
			}

			$end_time_part = microtime(true);
			$this->_add_performance_data('encode_post/languages', $end_time_part-$start_time_part);

			$start_time_part = microtime(true);

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

			$end_time_part = microtime(true);
			$this->_add_performance_data('encode_post/terms', $end_time_part-$start_time_part);

			$end_time = microtime(true);
			$this->_add_performance_data('encode_post', $end_time-$start_time);

			return $current_post_data;
		}

		public function encode_image($media_post) {
			//var_dump($media_post);
			
			if(!($media_post instanceof \WP_Post)) {
				return null;
			}

			$start_time = microtime(true);

			$media_post_id = $media_post->ID;
			$media_meta = get_post_meta($media_post_id, '_wp_attachment_metadata', true);
			

			$img_url = wp_get_attachment_url($media_post_id);
			$img_url_basename = wp_basename($img_url);

			$image_data = array();

			$image_data['id'] = $media_post_id;
			$image_data['title'] = get_the_title($media_post_id);
			$image_data['permalink'] = get_permalink($media_post_id);

			$image_data['alt'] = get_post_meta($media_post_id, '_wp_attachment_image_alt', true);
			$image_data['caption'] = $media_post->post_excerpt;
			$image_data['description'] = $media_post->post_content;
			
			if(isset($media_meta['sizes'])) {
				$sizes = $media_meta['sizes'];
				$image_size_data = array();
				if(is_array($sizes)) {
					foreach($sizes as $size_name => $size_data) {

						$current_url = str_replace( $img_url_basename, $size_data['file'], $img_url );

						$image_size_data[$size_name] = array('url' => $current_url, 'width' => $size_data['width'], 'height' =>  $size_data['height']);
					}
				}
			}

			$image_size_data['full'] = array('url' => $img_url, 'width' => $media_meta['width'], 'height' => $media_meta['height']);

			$image_data["sizes"] = $image_size_data;
			
			$image_data = apply_filters('m_router_data/add_image_meta', $image_data, $media_post_id, $media_post);

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

			if($value === null) {
				//MENOTE: do nothing
			}
			else if(is_array($value)) {

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
		
		protected function _get_field_value($field, $post_id, $type, $override_value = null) {
			$field_value = null;
			if($override_value) {
				$field_value = $override_value;
				
				if($type === 'wysiwyg' && !empty($field_value) ) {
			
					// apply filters
					$field_value = apply_filters( 'acf_the_content', $field_value );
		
		
					// follow the_content function in /wp-includes/post-template.php
					$field_value = str_replace(']]>', ']]&gt;', $field_value);
		
				}
			}
			else {
				//METODO: this needs to be unified
				//$field_value = $field['value'];
				
				if($field['value']) {
					$field_value = $field['value'];
					if($type === 'wysiwyg' && !empty($field_value) ) {
			
						// apply filters
						$field_value = apply_filters( 'acf_the_content', $field_value );
		
		
						// follow the_content function in /wp-includes/post-template.php
						$field_value = str_replace(']]>', ']]&gt;', $field_value);
		
					}
				}
				else {
					$acf_field = acf_get_field( $field['key'] );
					$field_value = acf_get_value( $post_id, $acf_field );
				
					if($type === 'page_link') {
						if( empty($field_value) ) {
							return null;
						}
					
						//METODO: support multiple values
						$field_value = intval($field_value);
					}
					else {
						$field_value = acf_format_value( $field_value, $post_id, $field );
					}
				}

				
				
				
			}
			
			return $field_value;
		}

		public function encode_acf_field($field, $post_id, $override_value = null) {
			//echo('encode_acf_field');
			//var_dump($field);

			$return_object = array();

			$type = $field['type'];
			$return_object['type'] = $type;

			switch($type) {
				case 'flexible_content':
					//var_dump($field);
					
					$rows_array = array();
					$current_key = $field['key'];
					
					$start_time_repeater = microtime(true);
					
					if(have_rows($current_key, $post_id)) {
						while(have_rows($current_key, $post_id)) {

							the_row();
							$current_row = get_row();
							
							$selected_template = null;
							

							$row_result = array();

							foreach($current_row as $key => $value) {
								
								if($key === 'acf_fc_layout') {
									$selected_template = $value;
								}
								else {
									$current_row_field = get_field_object($key, $post_id, false, true);
									$row_result[$current_row_field['name']] = $this->encode_acf_field($current_row_field, $post_id, $value);
								}
							}
							
							$typed_object = array('type' => 'flexible_content_selection', 'selectedTemplate' => $selected_template, 'value' => $row_result);
							array_push($rows_array, $typed_object);
						}
					}
					
					$end_time_repeater = microtime(true);
					$this->_add_performance_data('encode_acf_field/flexible_content', $end_time_repeater-$start_time_repeater);
					$this->_add_performance_data('encode_acf_field/flexible_content/'.$current_key, $end_time_repeater-$start_time_repeater);

					$return_object['value'] = $rows_array;
					break;
					
				case 'repeater':
				
					$rows_array = array();
					$current_key = $field['key'];

					$start_time_repeater = microtime(true);
					
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

					$end_time_repeater = microtime(true);
					$this->_add_performance_data('encode_acf_field/repeater', $end_time_repeater-$start_time_repeater);
					$this->_add_performance_data('encode_acf_field/repeater/'.$current_key, $end_time_repeater-$start_time_repeater);

					$return_object['value'] = $rows_array;
					break;
				case 'group':
					$row_result = array();
					$current_key = $field['key'];

					if(have_rows($current_key, $post_id)) {
						the_row();
						$current_row = get_row();
						
						foreach($current_row as $key => $value) {
							$current_row_field = get_field_object($key, $post_id, false, true);
							$row_result[$current_row_field['name']] = $this->encode_acf_field($current_row_field, $post_id, $value);
						}
					}

					$return_object['value'] = $row_result;
					break;
				case 'image':
					$field_value = $this->_get_field_value($field, $post_id, $type, $override_value);
					if(is_array($field_value)) {
						$return_object['value'] = $this->_encode_acf_image($field_value['id']);
					}
					else {
						$return_object['value'] = $this->_encode_acf_image($field_value);
					}
					break;
				case 'gallery':
					$field_value = $this->_get_field_value($field, $post_id, $type, $override_value);
					$return_object['value'] = $this->_encode_acf_image($field_value);
					break;
				case 'file':
					$field_value = $this->_get_field_value($field, $post_id, $type, $override_value);
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
				case 'page_link':
					$field_value = $this->_get_field_value($field, $post_id, $type, $override_value);
					$return_object['value'] = $this->_encode_acf_post_object($field_value);
					break;
				case 'post_object':
				case 'relationship':
					$field_value = $this->_get_field_value($field, $post_id, $type, $override_value);
					$return_object['value'] = $this->_encode_acf_post_object($field_value);
					break;
				case 'taxonomy':
					$field_value = $this->_get_field_value($field, $post_id, $type, $override_value);
					$taxonomy = $field['taxonomy'];
					$return_object['value'] = $this->_encode_acf_taxonomy($field_value, $taxonomy);
					break;
				case "oembed":
					$field_value = $this->_get_field_value($field, $post_id, $type, $override_value);
					if($field_value) {
						$start_time_oembed = microtime(true);
						$return_object['value'] = array('url' => $field_value, 'oembed' => wp_oembed_get($field_value));
						$end_time_oembed = microtime(true);
						$this->_add_performance_data('encode_acf_field/oembed', $end_time_oembed-$start_time_oembed);
					}
					else {
						$return_object['value'] = null;
					}
					break;
				case "user":
					$field_value = $this->_get_field_value($field, $post_id, $type, $override_value);
					if($field_value) {
						$return_object['value'] = $this->encode_user(get_user_by('id', $field_value));
					}
					else {
						$return_object['value'] = null;
					}
					break;
				default:
					$field_value = $this->_get_field_value($field, $post_id, $type, $override_value);
					$return_object['value'] = $field_value;
					break;
			}

			return $return_object;
		}
		
		protected function get_acf_field_object_by_key($key, $field_objects) {
			foreach($field_objects as $field_object) {
				if($field_object['key'] === $key) {
					return $field_object;
				}
			}
			
			return null;
		}
		
		public function encode_acf_value($unencoded_value, $field_object, $post_id) {
			switch($field_object['type']) {
				case 'true_false':
					return ($unencoded_value === '1');
				case 'repeater':
					$return_array = array();
					foreach($unencoded_value as $row) {
						$row_object = array();
						foreach($row as $field_key => $value) {
							$current_field_object = $this->get_acf_field_object_by_key($field_key, $field_object['sub_fields']);
							$current_name = $current_field_object['name'];
							$row_object[$current_name] = $this->encode_acf_value($value, $current_field_object, $post_id);
						}
						$return_array[] = $row_object;
					}
					return $return_array;
				case 'taxonomy':
					$taxonomy = $field_object['taxonomy'];
					if(is_array($unencoded_value)) {
						$return_array = array();
						foreach($unencoded_value as $current_id) {
							$current_term = get_term_by('id', $current_id, $taxonomy);
							$return_array[] = $this->encode_term($current_term);
						}
						
						return $return_array;
					}
					else {
						$current_term = get_term_by('id', $unencoded_value, $taxonomy);
						return array($this->encode_term($current_term));
					}
				case 'wysiwyg':
					return apply_filters('the_content', $unencoded_value);
				case 'user':
					$user = get_user_by('id', $unencoded_value);
					if($user) {
						return $this->encode_user($user);
					}
					else {
						return null;
					}
				case 'number':
					return (float)$unencoded_value;
				case 'text':
				case 'time_picker':
				case 'date_picker':
					return $unencoded_value;
			}
			
			//var_dump($unencoded_value, $field_object);
			
			return $unencoded_value;
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

			$start_time = microtime(true);

			$return_object = array();

			$queried_object = get_queried_object();
			$return_object['id'] = $term->term_id;
			$return_object['permalink'] = get_term_link($term);
			$return_object['name'] = $term->name;
			$return_object['slug'] = $term->slug;
			$return_object['description'] = $term->description;
			$return_object['taxonomy'] = $term->taxonomy;
			$return_object['parentId'] = $term->parent;

			$return_object["meta"] = get_term_meta($term->term_id);
			
			$return_object = apply_filters('m_router_data/encode_term', $return_object, $term->term_id, $term, $this);

			$end_time = microtime(true);
			$this->_add_performance_data('encode_term', $end_time-$start_time);

			return $return_object;
		}
		
		public function encode_term_link($term) {

			$start_time = microtime(true);

			$return_object = array();

			$queried_object = get_queried_object();
			$return_object['id'] = $term->term_id;
			$return_object['permalink'] = get_term_link($term);
			$return_object['name'] = $term->name;
			$return_object['slug'] = $term->slug;
			$return_object['taxonomy'] = $term->taxonomy;
			$return_object['parentId'] = $term->parent;
			
			$return_object = apply_filters('m_router_data/encode_term_link', $return_object, $term->term_id, $term, $this);

			$end_time = microtime(true);
			$this->_add_performance_data('encode_term_link', $end_time-$start_time);

			return $return_object;
		}

		public function encode_user($user) {

			if(!$user) {
				return null;
			}

			$start_time = microtime(true);

			$return_object = array();

			$return_object['id'] = $user->ID;
			$return_object['permalink'] = get_author_posts_url($user->ID);
			$return_object['name'] = $user->display_name;

			$return_object['gravatarHash'] = md5( strtolower( trim( $user->email ) ) );

			$end_time = microtime(true);
			$this->_add_performance_data('encode_user', $end_time-$start_time);

			return $return_object;
		}
		
		public function encode_user_with_private_data($user) {

			if(!$user) {
				return null;
			}

			$start_time = microtime(true);

			$return_object = array();

			$return_object['id'] = $user->ID;
			$return_object['permalink'] = get_author_posts_url($user->ID);
			$return_object['firstName'] = $user->first_name;
			$return_object['lastName'] = $user->last_name;
			$return_object['name'] = $user->display_name;
			$return_object['email'] = $user->user_email;
			
			$return_object['gravatarHash'] = md5( strtolower( trim( $user->email ) ) );

			$end_time = microtime(true);
			$this->_add_performance_data('encode_user', $end_time-$start_time);

			return $return_object;
		}
		
		public function encode_comment($comment) {
			$start_time = microtime(true);
			
			$return_object = array();
			
			//var_dump($comment);
			
			$return_object['id'] = $comment->comment_ID;
			$return_object['name'] = $comment->comment_author;
			$return_object['url'] = $comment->comment_author_url;
			$return_object['gravatarHash'] = md5( strtolower( trim( $comment->comment_author_email ) ) );
			$return_object['content'] = $comment->comment_content;
			$return_object['date'] = $comment->comment_date;
			
			if((int)$comment->user_id > 0) {
				$author = get_user_by('ID', $comment->user_id);
				$return_object["author"] = $this->encode_user($author);
			}
			else {
				$return_object["author"] = null;
			}
			
			$encoded_children = array();
			$children = $comment->get_children(array('status' => 'approve'));
			
			foreach($children as $child) {
				$encoded_children[] = $this->encode_comment($child);
			}
			
			$return_object['children'] = $encoded_children;
			
			$end_time = microtime(true);
			$this->_add_performance_data('encode_comment', $end_time-$start_time);

			return $return_object;
		}
		
		public function encode_acf_options() {
			$fields_object = get_field_objects('option', false, true);
			
			if($fields_object !== false) {

				$acf_object = array();
				foreach($fields_object as $name => $field_object) {
					$acf_object[$name] = $this->encode_acf_field($field_object, 'option');
				}

				return $acf_object;
			}
			
			return null;
		}

		public function encode() {

			$start_time = microtime(true);

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
				
				if(defined('ICL_LANGUAGE_CODE')) {
					$query_data['language'] = ICL_LANGUAGE_CODE;
				}

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

			$end_time = microtime(true);
			$this->_add_performance_data('encode', $end_time-$start_time);

			return $data;
		}

		public static function test_import() {
			echo("Imported \MRouterData\MRouterDataEncoder<br />");
		}
	}
?>
