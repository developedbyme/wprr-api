<?php
	namespace Wprr\DataApi\WordPress\ObjectRelation;

	// \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationType
	class ObjectRelationType {
		
		protected $_direction = null;
		protected $_type = null;
		protected $_relations = array();
		
		function __construct() {
			
		}
		
		public function get_direction() {
			return $this->_direction;
		}
		
		public function setup($direction, $type) {
			$this->_direction = $direction;
			$this->_type = $type;
			
			return $this;
		}
		
		public function add_relation($post) {
			
			$new_relation = new \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelation();
			
			$new_relation->setup($this, $post);
			$this->_relations[] = $new_relation;
			
			return $this;
		}
		
		public function filter_relations_by_object_type($relations, $object_type) {
			global $wprr_data_api;
			$wp = $wprr_data_api->wordpress();
			
			$return_array = array();
			
			$object_type_term = $wp->get_taxonomy('dbm_type')->get_term($object_type);
			
			foreach($relations as $relation) {
				if($relation->has_object_type($object_type_term)) {
					$return_array[] = $relation;
				}
			}
			
			return $return_array;
		}
		
		public function filter_relations_by_time($relations, $time) {
			$return_array = array();
			
			foreach($relations as $relation) {
				if($relation->is_active_at($time)) {
					$return_array[] = $relation;
				}
			}
			
			return $return_array;
		}
		
		public function get_relations($object_type, $time = -1) {
			$selected_relations = $this->_relations;
			if($object_type !== '*') {
				$selected_relations = $this->filter_relations_by_object_type($selected_relations, $object_type);
			}
			
			if($time !== false) {
				if($time === -1) {
					$time = time();
				}
				$selected_relations = $this->filter_relations_by_time($selected_relations, $time);
			}
			
			return $selected_relations;
		}
		
		public function get_object_ids($object_type, $time = -1) {
			
			$return_array = array();
			
			$selected_relations = $this->get_relations($object_type, $time);
			
			foreach($selected_relations as $relation) {
				$return_array[] = $relation->get_object_id();
			}
			
			return $return_array;
		}
		
		public function get_single_object_id($object_type, $time = -1) {
			$selected_relations = $this->get_relations($object_type, $time);
			
			if(!empty($selected_relations)) {
				return $selected_relations[0]->get_object_id();
			}
			
			return 0;
		}
		
		public function __toString() {
			return "[Type type:".$this->_type."]";
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\ObjectRelationType<br />");
		}
	}
?>