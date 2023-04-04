<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\Post
	class Post {
		
		protected $_id = 0;
		protected $_database_data = null;
		protected $_database_meta = null;
		protected $_meta = array();
		protected $_database_taxonomy_terms = null;
		protected $_taxonomy_terms = array();
		
		protected $_incomingRelations = null;
		protected $_outgoingRelations = null;
		protected $_userRelations = null;
		
		protected $_fields = null;
		
		function __construct() {
			
		}
		
		public function setup($id) {
			$this->_id = $id;
			
			return $this;
		}
		
		public function get_id() {
			return $this->_id;
		}
		
		public function editor() {
			global $wprr_data_api;
			return $wprr_data_api->wordpress()->editor()->get_post_editor($this->get_id());
		}
		
		public function get_database_data() {
			if(!$this->_database_data) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$query = 'SELECT * FROM '.DB_TABLE_PREFIX.'posts WHERE ID = "'.$this->_id.'"';
				$this->_database_data = $db->query_first($query);
			}
			
			return $this->_database_data;
		}
		
		public function has_database_meta_data() {
			return !!$this->_database_meta;
		}
		
		public function has_parsed_meta($key) {
			return isset($this->_meta[$key]);
		}
		
		public function get_database_meta_data() {
			if(!$this->_database_meta) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$query = 'SELECT meta_key, meta_value FROM '.DB_TABLE_PREFIX.'postmeta WHERE post_id = "'.$this->_id.'"';
				$this->_database_meta = $db->query_without_storage($query);
			}
			
			return $this->_database_meta;
		}
		
		public function set_database_meta_data($data) {
			$this->_database_meta = $data;
			
			return $this;
		}
		
		public function set_parsed_meta_array($key, $array) {
			$this->_meta[$key] = $array;
			
			return $this;
		}
		
		public function set_parsed_meta($key, $value) {
			$this->set_parsed_meta_array($key, array($value));
			
			return $this;
		}
		
		public function invalidate_meta() {
			$this->_database_meta = null;
			$this->_meta = array();
			
			return $this;
		}
		
		public function has_database_taxonomy_terms() {
			return !!$this->_database_taxonomy_terms;
		}
		
		public function get_database_taxonomy_terms() {
			if(!$this->_database_taxonomy_terms) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$query = 'SELECT '.DB_TABLE_PREFIX.'term_relationships.term_taxonomy_id, '.DB_TABLE_PREFIX.'term_taxonomy.taxonomy FROM '.DB_TABLE_PREFIX.'term_relationships INNER JOIN '.DB_TABLE_PREFIX.'term_taxonomy WHERE '.DB_TABLE_PREFIX.'term_relationships.term_taxonomy_id = '.DB_TABLE_PREFIX.'term_taxonomy.term_taxonomy_id AND '.DB_TABLE_PREFIX.'term_relationships.object_id = "'.$this->_id.'"';
				$this->_database_taxonomy_terms = $db->query_without_storage($query);
			}
			
			return $this->_database_taxonomy_terms;
		}
		
		public function set_database_taxonomy_terms($data) {
			$this->_database_taxonomy_terms = $data;
			
			return $this;
		}
		
		public function get_post_title() {
			return $this->get_data('post_title');
		}
		
		public function get_post_content() {
			return $this->get_data('post_content');
		}
		
		public function get_data($field) {
			$data = $this->get_database_data();
			
			return $data[$field];
		}
		
		public function get_meta_array($name) {
			if(!isset($this->_meta[$name])) {
				$meta_data = $this->get_database_meta_data();
				
				$selected_meta = array();
				
				foreach($meta_data as $meta_data_row) {
					if($meta_data_row['meta_key'] === $name) {
						$value = $meta_data_row['meta_value'];
						
						if(isset($value[1]) && $value[1] === ":") {
							$unserialize_value = unserialize($value);
							if($unserialize_value !== false) {
								$value = $unserialize_value;
							}
						}
						
						$selected_meta[] = $value;
					}
				}
				
				$this->_meta[$name] = $selected_meta;
			}
			
			return $this->_meta[$name];
		}
		
		public function get_meta($name) {
			
			$return_array = $this->get_meta_array($name);
			
			if(empty($return_array)) {
				return null;
			}
			
			return $return_array[0];
		}
		
		public function get_acf_repeater_meta($name, $fields) {
			
			$return_array = array();
			
			$length = (int)$this->get_meta($name);
			
			for($i = 0; $i < $length; $i++) {
				$encoded_object = array();
				
				foreach($fields as $field) {
					$encoded_object[$field] = $this->get_meta($name.'_'.$i.'_'.$field);
				}
				
				$return_array[] = $encoded_object;
			}
			
			return $return_array;
		}
		
		public function get_taxonomy_terms($taxonomy_name) {
			if(!isset($this->_taxonomy_terms[$taxonomy_name])) {
				global $wprr_data_api;
				
				$terms_data = $this->get_database_taxonomy_terms();
				
				$selected_terms = array();
				foreach($terms_data as $terms_data_row) {
					if($terms_data_row['taxonomy'] === $taxonomy_name) {
						$term_id = (int)$terms_data_row['term_taxonomy_id'];
						$selected_terms[] = $wprr_data_api->wordpress()->get_taxonomy($taxonomy_name)->get_term_by_id($term_id);
					}
				}
				
				$this->_taxonomy_terms[$taxonomy_name] = $selected_terms;
			}
			
			return $this->_taxonomy_terms[$taxonomy_name];
		}
		
		public function get_single_term_in($group_term) {
			$taxonomy_name = $group_term->get_taxonomy()->get_name();
			$terms = $this->get_taxonomy_terms($taxonomy_name);
			foreach($terms as $term) {
				if($term->get_parent() === $group_term) {
					return $term;
				}
			}
			
			return null;
		}
		
		public function get_terms_in($group_term) {
			
			$return_array = array();
			
			$taxonomy_name = $group_term->get_taxonomy()->get_name();
			$terms = $this->get_taxonomy_terms($taxonomy_name);
			foreach($terms as $term) {
				if($term->get_parent() === $group_term) {
					$return_array[] = $term;
				}
			}
			
			return $return_array;
		}
		
		public function get_single_term_in_with_descendants($group_term) {
			$taxonomy_name = $group_term->get_taxonomy()->get_name();
			$terms = $this->get_taxonomy_terms($taxonomy_name);
			foreach($terms as $term) {
				if($term->is_descendant_of($group_term)) {
					return $term;
				}
			}
			
			return null;
		}
		
		public function get_terms_in_with_descendants($group_term) {
			$return_array = array();
			
			$taxonomy_name = $group_term->get_taxonomy()->get_name();
			$terms = $this->get_taxonomy_terms($taxonomy_name);
			foreach($terms as $term) {
				if($term->is_descendant_of($group_term)) {
					$return_array[] = $term;
				}
			}
			
			return $return_array;
		}
		
		public function has_term($term) {
			$taxonomy_name = $term->get_taxonomy()->get_name();
			$terms = $this->get_taxonomy_terms($taxonomy_name);
			foreach($terms as $current_term) {
				if($current_term === $term) {
					return true;
				}
			}
			
			return false;
		}
		
		public function get_active_taxonomy_names() {
			global $wprr_data_api;
			
			$terms_data = $this->get_database_taxonomy_terms();
			$taxonomy_names = array();
			
			foreach($terms_data as $terms_data_row) {
				$current_name = $terms_data_row['taxonomy'];
				if(!in_array($current_name, $taxonomy_names)) {
					$taxonomy_names[] = $current_name;
				}
			}
			
			return $taxonomy_names;
		}
		
		public function get_featured_image() {
			$image_id = (int)$this->get_meta('_thumbnail_id');
			if(!$image_id) {
				return null;
			}
			
			global $wprr_data_api;
			return $wprr_data_api->wordpress()->get_post($image_id);
		}
		
		public function get_parent() {
			$data = $this->get_database_data();
			$parent_id = (int)$data['post_parent'];
			
			if(!$parent_id) {
				return null;
			}
			
			global $wprr_data_api;
			return $wprr_data_api->wordpress()->get_post($parent_id);
		}
		
		public function get_link() {
			
			$parent_path = '';
			$parent = $this->get_parent();
			if($parent) {
				$parent_path = $parent->get_link();
			}
			
			return $parent_path.$this->get_slug().'/';
		}
		
		public function get_slug() {
			$data = $this->get_database_data();
			
			return $data['post_name'];
		}
		
		public function get_publish_date() {
			$data = $this->get_database_data();
			
			return $data['post_date'];
		}
		
		public function get_incoming_direction() {
			if(!$this->_incomingRelations) {
				$this->_incomingRelations = new \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationDirection();
				$this->_incomingRelations->setup($this, 'incoming');
			}
			
			return $this->_incomingRelations;
		}
		
		public function get_order($for_type) {
			
			global $wprr_data_api;
			$wp = $wprr_data_api->wordpress();
			
			$order_ids = $this->get_outgoing_direction()->get_type('relation-order-by')->get_object_ids('relation-order');
		
			foreach($order_ids as $order_id) {
				
				$post = $wp->get_post($order_id);
				
				$current_for_type = $post->get_meta('forType');
			
				if($current_for_type === $for_type) {
					return $post->get_meta('order');
				}
			}
		
			return array();
		}
		
		public function get_outgoing_direction() {
			if(!$this->_outgoingRelations) {
				$this->_outgoingRelations = new \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationDirection();
				$this->_outgoingRelations->setup($this, 'outgoing');
			}
			
			return $this->_outgoingRelations;
		}
		
		public function get_user_relations() {
			if(!$this->_userRelations) {
				$this->_userRelations = new \Wprr\DataApi\WordPress\ObjectRelation\ObjectUserRelationDirection();
				$this->_userRelations->setup($this);
			}
			
			return $this->_userRelations;
		}
		
		public function get_fields() {
			//echo("Post::get_fields\n");
			
			if(!$this->_fields) {
				$this->_fields = new \Wprr\DataApi\WordPress\Field\Fields();
				$this->_fields->setup($this);
			}
			
			return $this->_fields;
		}
		
		public function has_object_relation($path) {
			$items = $this->object_relation_query($path);
			
			return !empty($items);
		}
		
		public function object_relation_query($path) {
			return \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationQuery::get_posts(array($this), $path);
		}
		
		public function single_object_relation_query($path) {
			return \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationQuery::get_single_post($this, $path);
		}
		
		public function has_object_relation_meta($path, $key, $value) {
			$items = $this->object_relation_query($path);
			
			foreach($items as $item) {
				if($item->get_meta($key) == $value) {
					return true;
				}
			}
			
			return false;
		}
		
		public function object_relation_query_with_meta_filter($path, $key, $value) {
			$items = $this->object_relation_query($path);
			
			$matching_items = array();
			
			foreach($items as $item) {
				if($item->get_meta($key) == $value) {
					$matching_items[] = $item;
				}
			}
			
			return $matching_items;
		}
		
		public function single_object_relation_query_with_meta_filter($path, $key, $value) {
			$items = $this->object_relation_query($path);
			
			$matching_items = array();
			
			foreach($items as $item) {
				if($item->get_meta($key) == $value) {
					return $item;
				}
			}
			
			return null;
		}
		
		public function clear_object_relation_cache() {
			$this->_incomingRelations = null;
			$this->_outgoingRelations = null;
			$this->_userRelations = null;
			
			return $this;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Post<br />");
		}
	}
?>