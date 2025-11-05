<?php
	namespace Wprr\DataApi\WordPress\ObjectRelation;

	// \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationType
	class ObjectRelationType {
		
		protected $_direction = null;
		protected $_type;
		protected $_relations;
		
		function __construct() {
			
		}
		
		public function get_direction() {
			return $this->_direction;
		}
		
		public function get_type():string {
			return $this->_type;
		}
		
		public function setup($direction, $type):ObjectRelationType {
			$this->_direction = $direction;
			$this->_type = $type;
			
			return $this;
		}
		
		public function get_all_relations():array {
			global $wprr_data_api;
			
			if(!isset($this->_relations)) {
				
				$wp = $wprr_data_api->wordpress();
				
				$this->_relations = array();
				
				$field = 'toId';
				$reverse_field = 'fromId';
				if($this->_direction->get_identifier() === 'outgoing') {
					$field = 'fromId';
					$reverse_field = 'toId';
				}
				
				if(defined("READ_OBJECT_RELATION_TABLES") && READ_OBJECT_RELATION_TABLES) {
					$wprr_data_api->performance()->start_meassure('ObjectRelationType::get_all_relations (tables)');
					
					$type = $this->get_type();
					$post_id = $this->_direction->get_post()->get_id();
					$relations_table = DB_TABLE_PREFIX."dbm_object_relations";
					$types_table = DB_TABLE_PREFIX."dbm_object_relation_types";
					$posts_table = DB_TABLE_PREFIX."posts";
					$sql = "SELECT $relations_table.id as id, $relations_table.$reverse_field as linkedId, $relations_table.startAt as startAt, $relations_table.endAt as endAt FROM $relations_table INNER JOIN $posts_table ON $relations_table.id = $posts_table.ID INNER JOIN $types_table ON $relations_table.type = $types_table.id WHERE $relations_table.$field = $post_id AND $posts_table.post_status IN ('publish', 'private') AND $types_table.path = '$type'";
					$relations = $wprr_data_api->database()->query_without_storage($sql);
					
					foreach($relations as $relation_data) {
						$relation_post = $wp->get_post((int)$relation_data['id']);
						$relation_post->set_parsed_meta($field, $post_id);
						$relation_post->set_parsed_meta($reverse_field, (int)$relation_data['linkedId']);
						$relation_post->set_parsed_meta('startAt', (int)$relation_data['startAt']);
						$relation_post->set_parsed_meta('endAt', (int)$relation_data['endAt']);
						$relation_post->set_parsed_meta('type', $type);
						$this->add_relation($relation_post);
					}
					
					$wprr_data_api->performance()->stop_meassure('ObjectRelationType::get_all_relations (tables)');
				}
				else {
				
					$wprr_data_api->performance()->start_meassure('ObjectRelationType::get_all_relations');
				
					$query = new \Wprr\DataApi\Data\Range\SelectQuery();
				
					$wprr_data_api->performance()->start_meassure('ObjectRelationType::get_all_relations get ids');
				
					$query->set_post_type('dbm_object_relation')->include_private();
				
					$query->term_query_by_path('dbm_type', 'object-relation/'.$this->get_type());
				
					//$query->meta_query($field, $this->_direction->get_post()->get_id());
					$query->meta_query_join($field, $this->_direction->get_post()->get_id());
				
					$ids = $query->get_ids_without_storage();
				
					$wprr_data_api->performance()->stop_meassure('ObjectRelationType::get_all_relations get ids');
					$wprr_data_api->performance()->start_meassure('ObjectRelationType::get_all_relations load meta');
				
					$wp->load_meta_for_relations($ids);
				
					$wprr_data_api->performance()->stop_meassure('ObjectRelationType::get_all_relations load meta');
				
					$wprr_data_api->performance()->start_meassure('ObjectRelationType::get_all_relations setup relations');
					foreach($ids as $id) {
						$post = $wp->get_post($id);
						$this->add_relation($post);
					}
					$wprr_data_api->performance()->stop_meassure('ObjectRelationType::get_all_relations setup relations');
				
					$wprr_data_api->performance()->stop_meassure('ObjectRelationType::get_all_relations');
				}
			}
			
			return $this->_relations;
		}
		
		public function add_relation($post) {
			
			$new_relation = new \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelation();
			
			$new_relation->setup($this, $post);
			$this->_relations[] = $new_relation;
			
			return $new_relation;
		}
		
		public function load_object_types():ObjectRelationType {
			
			global $wprr_data_api;
			
			$relations = $this->get_all_relations();
			$ids = array_map(function($item) {return $item->get_object_id();}, $relations);
			
			$wprr_data_api->wordpress()->load_taxonomy_terms_for_posts($ids);
			
			return $this;
		}
		
		public function filter_relations_by_object_type(array $relations, string $object_type):array {
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
		
		public function filter_relations_by_time(array $relations, int $time):array {
			$return_array = array();
			
			foreach($relations as $relation) {
				if($relation->is_active_at($time)) {
					$return_array[] = $relation;
				}
			}
			
			return $return_array;
		}
		
		public function get_relations(string $object_type, $time = -1):array {
			$selected_relations = $this->get_all_relations();
			
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
		
		public function get_object_ids(string $object_type, $time = -1):array {
			
			$return_array = array();
			
			$selected_relations = $this->get_relations($object_type, $time);
			
			foreach($selected_relations as $relation) {
				$return_array[] = $relation->get_object_id();
			}
			
			return $return_array;
		}
		
		public function get_single_object_id(string $object_type, $time = -1):int {
			$selected_relations = $this->get_relations($object_type, $time);
			
			if(!empty($selected_relations)) {
				return $selected_relations[0]->get_object_id();
			}
			
			return 0;
		}
		
		public function get_object_ids_in_order(string $object_type, string $order, $time = -1):array {
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
		
		protected function _update_hierarchy_order(&$hierarchy_items, &$active_ids, &$unused_ids, &$id_map):void {
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
		
		public function get_object_ids_in_hierarchy(string $object_type, $order, $time = -1):array {
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