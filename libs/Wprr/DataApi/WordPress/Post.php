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
		
		function __construct() {
			
		}
		
		public function setup($id) {
			$this->_id = $id;
			
			return $this;
		}
		
		public function get_id() {
			return $this->_id;
		}
		
		public function get_database_data() {
			if(!$this->_database_data) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$query = 'SELECT * FROM wp_posts WHERE ID = "'.$this->_id.'"';
				$this->_database_data = $db->query_first($query);
			}
			
			return $this->_database_data;
		}
		
		public function get_database_meta_data() {
			if(!$this->_database_meta) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$query = 'SELECT meta_key, meta_value FROM wp_postmeta WHERE post_id = "'.$this->_id.'"';
				$this->_database_meta = $db->query($query);
			}
			
			return $this->_database_meta;
		}
		
		public function get_database_taxonomy_terms() {
			if(!$this->_database_taxonomy_terms) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$query = 'SELECT wp_term_relationships.term_taxonomy_id, wp_term_taxonomy.taxonomy FROM wp_term_relationships INNER JOIN wp_term_taxonomy WHERE wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id AND wp_term_relationships.object_id = "'.$this->_id.'"';
				$this->_database_taxonomy_terms = $db->query($query);
			}
			
			return $this->_database_taxonomy_terms;
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
		
		public function get_meta($name) {
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
			
			return $this->_meta[$name][0];
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
		
		public function get_incoming_direction() {
			if(!$this->_incomingRelations) {
				$this->_incomingRelations = new \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationDirection();
				$this->_incomingRelations->setup($this, 'incoming');
			}
			
			return $this->_incomingRelations;
		}
		
		public function get_outcoming_direction() {
			if(!$this->_outgoingRelations) {
				$this->_outgoingRelations = new \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationDirection();
				$this->_outgoingRelations->setup($this, 'outgoing');
			}
			
			return $this->_outgoingRelations;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Post<br />");
		}
	}
?>