<?php
	namespace Wprr\DataApi\WordPress\ObjectRelation;

	// \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationDirection
	class ObjectRelationDirection {
		
		protected $_post = null;
		protected $_direction = null;
		
		protected $_types = null;
		
		function __construct() {
			
		}
		
		public function get_identifier() {
			return $this->_direction;
		}
		
		public function setup($post, $direction) {
			$this->_post = $post;
			$this->_direction = $direction;
			
			return $this;
		}
		
		protected function ensure_type($type) {
			if(!isset($this->_types[$type])) {
				$new_type = new \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationType();
				$new_type->setup($this, $type);
				$this->_types[$type] = $new_type;
			}
			
			return $this->_types[$type];
		}
		
		public function get_types() {
			if($this->_types === null) {
				$this->_types = array();
				
				$query = new \Wprr\DataApi\Data\Range\SelectQuery();
				
				$field = 'toId';
				if($this->_direction === 'outgoing') {
					$field = 'fromId';
				}
				
				$ids = $query->set_post_type('dbm_object_relation')->include_term_by_path('dbm_type', 'object-relation')->include_private()->meta_query($field, $this->_post->get_id())->get_ids();
				
				global $wprr_data_api;
				$wp = $wprr_data_api->wordpress();
				$group_term = $wp->get_taxonomy('dbm_type')->get_term('object-relation');
				
				$wp->load_meta_for_posts($ids);
				
				foreach($ids as $id) {
					$post = $wp->get_post($id);
					
					$type_terms = $post->get_terms_in($group_term);
					
					foreach($type_terms as $type_term) {
						$type = $type_term->get_slug();
						
						$object_relation_type = $this->ensure_type($type);
						$object_relation_type->add_relation($post);
						
						
					}
					
				}
			}
			
			return $this->_types;
		}
		
		public function get_type($type) {
			$types = $this->get_types();
			
			return $this->ensure_type($type);
		}
		
		public function __toString() {
			return "[Direction identifier:".$this->_direction."]";
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\ObjectRelationDirection<br />");
		}
	}
?>