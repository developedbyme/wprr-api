<?php
	namespace MRouterData;

	use \WP_Query;
	use \WP_Term;
	use \WP_Post;
	use \WP_User;

	// \MRouterData\MRouterDataEncoder
	class MRouterDataEncoder {

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
			
			$media_post_id = $media_post->ID;
			$media_meta = get_post_meta($media_post_id, '_wp_attachment_metadata', true);
			$sizes = $media_meta['sizes'];

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
					$image_url_and_size = wp_get_attachment_image_src($media_post_id, $size_name);
					$image_size_data[$size_name] = array('url' => $image_url_and_size[0], 'width' => $image_url_and_size[1], 'height' => $image_url_and_size[2]);
				}
			}
			
			$image_url_and_size = wp_get_attachment_image_src($media_post_id, 'full');
			$image_size_data['full'] = array('url' => $image_url_and_size[0], 'width' => $image_url_and_size[1], 'height' => $image_url_and_size[2]);

			$image_data["sizes"] = $image_size_data;

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
					$return_object['value'] = $this->_encode_acf_image($field_value);
					break;
				case 'post_object':
				case 'relationship':
					$return_object['value'] = $this->_encode_acf_post_object($field_value);
					break;
				case 'taxonomy': //METODO: implement this
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
			$return_object['description'] = $term->description;
			$return_object['taxonomy'] = $term->taxonomy;
			//METODO: add taxonomy name

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

		public static function test_import() {
			echo("Imported \MRouterData\MRouterDataEncoder<br />");
		}
	}
?>