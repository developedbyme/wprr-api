<?php
	namespace Wprr\DataApi\WordPress\ObjectRelation;

	// \Wprr\DataApi\WordPress\ObjectRelation\ObjectUserRelationDirection
	class ObjectUserRelationDirection {
		
		protected $_post = null;
		protected $_direction = null;
		
		protected $_types = null;
		
		function __construct() {
			
		}
		
		public function setup($post) {
			$this->_post = $post;
			
			return $this;
		}
		
		protected function ensure_type($type) {
			if(!isset($this->_types[$type])) {
				$new_type = new \Wprr\DataApi\WordPress\ObjectRelation\ObjectUserRelationFromObjectType();
				$new_type->setup($this, $type);
				$this->_types[$type] = $new_type;
			}
			
			return $this->_types[$type];
		}
		
		public function get_types() {
			if($this->_types === null) {
				
				global $wprr_data_api;
				
				$wprr_data_api->performance()->start_meassure('ObjectUserRelationDirection::get_types');
				
				$this->_types = array();
				
				$query = new \Wprr\DataApi\Data\Range\SelectQuery();
				
				$query->set_post_type('dbm_object_relation')->include_private();
				$query->term_query_by_path('dbm_type', 'object-user-relation');
				$query->meta_query('fromId', $this->_post->get_id());
				$ids = $query->get_ids();
				
				$wp = $wprr_data_api->wordpress();
				$group_term = $wp->get_taxonomy('dbm_type')->get_term('object-user-relation');
				
				foreach($ids as $id) {
					$post = $wp->get_post($id);
					
					$type_terms = $post->get_terms_in($group_term);
					
					foreach($type_terms as $type_term) {
						$type = $type_term->get_slug();
						
						$object_relation_type = $this->ensure_type($type);
						$object_relation_type->add_relation($post);
					}
				}
				
				$wprr_data_api->performance()->stop_meassure('ObjectUserRelationDirection::get_types');
			}
			
			return $this->_types;
		}
		
		public function get_type($type) {
			$types = $this->get_types();
			
			return $this->ensure_type($type);
		}
		
		public function __toString() {
			return "[ObjectUserRelationDirection]";
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\ObjectUserRelationDirection<br />");
		}
	}
?>