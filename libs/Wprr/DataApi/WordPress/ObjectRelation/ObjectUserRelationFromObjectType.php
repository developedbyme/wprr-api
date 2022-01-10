<?php
	namespace Wprr\DataApi\WordPress\ObjectRelation;

	// \Wprr\DataApi\WordPress\ObjectRelation\ObjectUserRelationFromObjectType
	class ObjectUserRelationFromObjectType {
		
		protected $_user = null;
		protected $_type = null;
		protected $_relations = array();
		
		function __construct() {
			
		}
		
		public function get_user() {
			return $this->_user;
		}
		
		public function setup($user, $type) {
			$this->_user = $user;
			$this->_type = $type;
			
			return $this;
		}
		
		public function add_relation($post) {
			
			$new_relation = new \Wprr\DataApi\WordPress\ObjectRelation\ObjectUserRelation();
			
			$new_relation->setup($this, $post);
			$this->_relations[] = $new_relation;
			
			return $new_relation;
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
		
		public function get_relations($time = -1) {
			$selected_relations = $this->_relations;
			
			if($time !== false) {
				if($time === -1) {
					$time = time();
				}
				$selected_relations = $this->filter_relations_by_time($selected_relations, $time);
			}
			
			return $selected_relations;
		}
		
		public function get_user_ids($time = -1) {
			
			$return_array = array();
			
			$selected_relations = $this->get_relations($time);
			
			foreach($selected_relations as $relation) {
				$return_array[] = $relation->get_user_id();
			}
			
			return $return_array;
		}
		
		public function get_single_user_id($time = -1) {
			$selected_relations = $this->get_relations($time);
			
			if(!empty($selected_relations)) {
				return $selected_relations[0]->get_user_id();
			}
			
			return 0;
		}
		
		public function __toString() {
			return "[ObjectUserRelationFromObjectType type:".$this->_type."]";
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\ObjectUserRelationFromObjectType<br />");
		}
	}
?>