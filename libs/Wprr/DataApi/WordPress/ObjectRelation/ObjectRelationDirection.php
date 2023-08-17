<?php
	namespace Wprr\DataApi\WordPress\ObjectRelation;

	// \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationDirection
	class ObjectRelationDirection {
		
		protected $_post = null;
		protected $_direction = null;
		
		protected array $_types = array();
		protected bool $_has_all_types = false;
		
		function __construct() {
			
		}
		
		public function get_identifier() {
			return $this->_direction;
		}
		
		public function get_post() {
			return $this->_post;
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
			if(!$this->_has_all_types) {
				
				global $wprr_data_api;
				
				$wprr_data_api->performance()->start_meassure('ObjectRelationDirection::get_types');
				
				$this->_has_all_types = true;
				
				$query = new \Wprr\DataApi\Data\Range\SelectQuery();
				
				$field = 'toId';
				$reverse_field = 'fromId';
				if($this->_direction === 'outgoing') {
					$field = 'fromId';
					$reverse_field = 'toId';
				}
				
				$wprr_data_api->performance()->start_meassure('ObjectRelationDirection::get_types get ids');
				$wprr_data_api->performance()->start_meassure('ObjectRelationDirection::get_types get ids 1');
				$query->set_post_type('dbm_object_relation')->include_private();
				$wprr_data_api->performance()->stop_meassure('ObjectRelationDirection::get_types get ids 1');
				$wprr_data_api->performance()->start_meassure('ObjectRelationDirection::get_types get ids 2');
				$query->term_query_by_path('dbm_type', 'object-relation');
				$wprr_data_api->performance()->stop_meassure('ObjectRelationDirection::get_types get ids 2');
				$wprr_data_api->performance()->start_meassure('ObjectRelationDirection::get_types get ids 3');
				$query->meta_query($field, $this->_post->get_id());
				//$query->meta_query_join($field, $this->_post->get_id());
				$wprr_data_api->performance()->stop_meassure('ObjectRelationDirection::get_types get ids 3');
				$wprr_data_api->performance()->start_meassure('ObjectRelationDirection::get_types get ids 4');
				$ids = $query->get_ids_without_storage();
				$wprr_data_api->performance()->stop_meassure('ObjectRelationDirection::get_types get ids 4');
				$wprr_data_api->performance()->stop_meassure('ObjectRelationDirection::get_types get ids');
				
				$wprr_data_api->performance()->start_meassure('ObjectRelationDirection::get_types get terms');
				$wp = $wprr_data_api->wordpress();
				$group_term = $wp->get_taxonomy('dbm_type')->get_term('object-relation');
				$wprr_data_api->performance()->stop_meassure('ObjectRelationDirection::get_types get terms');
				
				$wprr_data_api->performance()->start_meassure('ObjectRelationDirection::get_types load relation terms');
				$wp->load_taxonomy_terms_for_posts($ids);
				$wprr_data_api->performance()->stop_meassure('ObjectRelationDirection::get_types load relation terms');
				
				$types_to_add = array();
				
				$wprr_data_api->performance()->start_meassure('ObjectRelationDirection::get_types setup relations');
				foreach($ids as $id) {
					$post = $wp->get_post($id);
					
					$type_terms = $post->get_terms_in($group_term);
					
					foreach($type_terms as $type_term) {
						$type = $type_term->get_slug();
						$types_to_add[$type] = true;
					}
				}
				foreach(array_keys($types_to_add) as $type) {
					$object_relation_type = $this->ensure_type($type);
				}
				
				$wprr_data_api->performance()->stop_meassure('ObjectRelationDirection::get_types setup relations');
				
				$wprr_data_api->performance()->stop_meassure('ObjectRelationDirection::get_types');
			}
			
			return $this->_types;
		}
		
		public function get_type($type) {
			
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