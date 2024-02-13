<?php
	namespace Wprr\DataApi\WordPress\ObjectRelation;

	// \Wprr\DataApi\WordPress\ObjectRelation\ObjectUserRelationFromObjectType
	class ObjectUserRelationFromObjectType {
		
		protected $_direction = null;
		protected $_type = null;
		protected $_relations = null;
		
		function __construct() {
			
		}
		
		public function get_post() {
			return $this->_direction->get_post();
		}
		
		public function get_type():string {
			return $this->_type;
		}
		
		public function setup($direction, $type) {
			$this->_direction = $direction;
			$this->_type = $type;
			
			return $this;
		}
		
		public function get_all_relations():array {
			global $wprr_data_api;
			
			if(!isset($this->_relations)) {
				
				$wp = $wprr_data_api->wordpress();
				
				$this->_relations = array();
				
				if(defined("READ_OBJECT_RELATION_TABLES") && READ_OBJECT_RELATION_TABLES) {
					
					$type = $this->get_type();
					$post_id = $this->_direction->get_post()->get_id();
					$sql = "SELECT ".DB_TABLE_PREFIX."dbm_object_user_relations.id as id, ".DB_TABLE_PREFIX."dbm_object_user_relations.userId as linkedId, ".DB_TABLE_PREFIX."dbm_object_user_relations.startAt as startAt, ".DB_TABLE_PREFIX."dbm_object_user_relations.endAt as endAt FROM ".DB_TABLE_PREFIX."dbm_object_user_relations INNER JOIN ".DB_TABLE_PREFIX."posts ON ".DB_TABLE_PREFIX."dbm_object_user_relations.id = ".DB_TABLE_PREFIX."posts.ID INNER JOIN ".DB_TABLE_PREFIX."dbm_object_relation_types ON ".DB_TABLE_PREFIX."dbm_object_user_relations.type = ".DB_TABLE_PREFIX."dbm_object_relation_types.id WHERE ".DB_TABLE_PREFIX."dbm_object_user_relations.postId = $post_id AND ".DB_TABLE_PREFIX."posts.post_status IN ('publish', 'private') AND ".DB_TABLE_PREFIX."dbm_object_relation_types.path = '".$this->get_type()."'";
					$relations = $wprr_data_api->database()->query_without_storage($sql);
					
					foreach($relations as $relation_data) {
						$relation_post = $wp->get_post((int)$relation_data['id']);
						$relation_post->set_parsed_meta('fromId', $post_id);
						$relation_post->set_parsed_meta('toId', (int)$relation_data['linkedId']);
						$relation_post->set_parsed_meta('startAt', (int)$relation_data['startAt']);
						$relation_post->set_parsed_meta('endAt', (int)$relation_data['endAt']);
						$relation_post->set_parsed_meta('type', $type);
						$this->add_relation($relation_post);
					}
					
				}
				else {
				
					$query = new \Wprr\DataApi\Data\Range\SelectQuery();
				
					$query->set_post_type('dbm_object_relation')->include_private();
				
					$query->term_query_by_path('dbm_type', 'object-user-relation/'.$this->get_type());
				
					$query->meta_query('fromId', $this->_direction->get_post()->get_id());
				
					$ids = $query->get_ids_without_storage();
				
					$wp->load_meta_for_user_relations($ids);
					
					foreach($ids as $id) {
						$post = $wp->get_post($id);
						$this->add_relation($post);
					}
					
				}
			}
			
			return $this->_relations;
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
			$selected_relations = $this->get_all_relations();
			
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