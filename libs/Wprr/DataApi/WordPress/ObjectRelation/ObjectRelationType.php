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
		
		public function get_type() {
			return $this->_type;
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
			
			return $new_relation;
		}
		
		public function load_object_types() {
			
			global $wprr_data_api;
			
			$ids = array_map(function($item) {return $item->get_object_id();}, $this->_relations);
			
			$wprr_data_api->wordpress()->load_taxonomy_terms_for_posts($ids);
			
			return $this;
		}
		
		public function filter_relations_by_object_type($relations, $object_type) {
			global $wprr_data_api;
			$wp = $wprr_data_api->wordpress();
			
			$this->load_object_types();
			
			$return_array = array();
			
			$object_type_term = $wp->get_taxonomy('dbm_type')->get_term($object_type);
			if(!$object_type_term) {
				throw new \Exception('No object type named '.$object_type);
			}
			
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
		
		public function get_object_ids_in_order($object_type, $order, $time = -1) {
			$return_array = array();
			
			$selected_relations = $this->get_relations($object_type, $time);
			
			$order = $this->get_direction()->get_post()->get_order($order);
			
			$current_next_space = count($order);
			
			$return_array = array();
			
			foreach($selected_relations as $relation) {
				
				$current_id = $relation->get_id();
				$index = array_search($current_id, $order);
				if($index === false) {
					$index = $current_next_space;
					$current_next_space++;
				}
				
				$return_array[$index] = $relation->get_object_id();
			}
			
			ksort($return_array);
			
			return array_values($return_array);
		}
		
		protected function _update_hierarchy_order(&$hierarchy_items, &$active_ids, &$unused_ids, &$id_map) {
			//var_dump('_update_hierarchy_order', $hierarchy_items, $active_ids, $unused_ids, $id_map);
			
			$length = count($hierarchy_items);
			
			for($i = 0; $i < $length; $i++) {
				$hierarchy_item = &$hierarchy_items[$i];
				$id = $hierarchy_item["id"];
				if(array_search($id, $active_ids) !== false) {
					$hierarchy_item["id"] = $id_map[$id];
					
					$unused_index = array_search($id, $unused_ids);
					if($unused_index !== false) {
						array_splice($unused_ids, $unused_index, 1);
					}
					
					$this->_update_hierarchy_order($hierarchy_item["children"], $active_ids, $unused_ids, $id_map);
				}
				else {
					array_splice($hierarchy_items, $i, 1);
					$i--;
					$length--;
				}
			}
		}
		
		public function get_object_ids_in_hierarchy($object_type, $order, $time = -1) {
			$return_array = array();
			
			$selected_relations = $this->get_relations($object_type, $time);
			
			$order = $this->get_direction()->get_post()->get_order($order);
			
			$active_ids = array();
			$unused_ids = array();
			$id_map = array();
			
			foreach($selected_relations as $relation) {
				
				$current_id = $relation->get_id();
				
				$active_ids[] = $current_id;
				$unused_ids[] = $current_id;
				
				$id_map[$current_id] = $relation->get_object_id();
			}
			
			$this->_update_hierarchy_order($order, $active_ids, $unused_ids, $id_map);
			
			foreach($unused_ids as $unused_id) {
				$order[] = array('id' => $id_map[$unused_id], 'children' => array());
			}
			
			return $order;
		}
		
		public function __toString() {
			return "[Type type:".$this->_type."]";
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\ObjectRelationType<br />");
		}
	}
?>