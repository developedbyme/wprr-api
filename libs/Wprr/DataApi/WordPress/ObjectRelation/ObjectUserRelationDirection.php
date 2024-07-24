<?php
	namespace Wprr\DataApi\WordPress\ObjectRelation;

	// \Wprr\DataApi\WordPress\ObjectRelation\ObjectUserRelationDirection
	class ObjectUserRelationDirection {
		
		protected $_post = null;
		
		protected $_types = array();
		protected $_has_all_types = false;
		
		function __construct() {
			
		}
		
		public function setup($post) {
			$this->_post = $post;
			
			return $this;
		}
		
		public function get_post() {
			return $this->_post;
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
			if(!$this->_has_all_types) {
				
				global $wprr_data_api;
				
				$this->_has_all_types = true;
				
				$types_to_add = array();
				
				if(defined("READ_OBJECT_RELATION_TABLES") && READ_OBJECT_RELATION_TABLES) {
					$post_id = $this->_post->get_id();
					$types_table = DB_TABLE_PREFIX."dbm_object_relation_types";
					$relations_table = DB_TABLE_PREFIX."dbm_object_user_relations";
					$sql = "SELECT DISTINCT $types_table.path as path FROM $types_table INNER JOIN $relations_table ON $relations_table.type = $types_table.id WHERE $relations_table.postId = $post_id";
					$rows = $wprr_data_api->database()->query_without_storage($sql);
					$types_to_add = array_map(function($row) {return $row['path'];}, $rows);
				}
				else {
					$query = new \Wprr\DataApi\Data\Range\SelectQuery();
					$query->set_post_type('dbm_object_relation')->include_private();
				
					$query->term_query_by_path('dbm_type', 'object-user-relation');
				
					$query->meta_query('fromId', $this->_post->get_id());
				
					$ids = $query->get_ids_without_storage();
				
					$wp = $wprr_data_api->wordpress();
					$group_term = $wp->get_taxonomy('dbm_type')->get_term('object-user-relation');
				
					$wp->load_taxonomy_terms_for_posts($ids);
					
					foreach($ids as $id) {
						$post = $wp->get_post($id);
					
						$type_terms = $post->get_terms_in($group_term);
					
						foreach($type_terms as $type_term) {
							$type = $type_term->get_slug();
							$types_to_add[] = $type;
						}
						
						$types_to_add = array_unique($types_to_add);
					}
				}
				
				foreach($types_to_add as $type) {
					$object_relation_type = $this->ensure_type($type);
				}
			}
			
			return $this->_types;
		}
		
		public function get_type($type) {
			return $this->ensure_type($type);
		}
		
		public function get_all_relations():array {
			$return_array = array();
			
			$types = $this->get_types();
			if($types) {
				foreach($types as $type) {
				
					$relations = $type->get_all_relations();
				
					$return_array = array_merge($return_array, $relations);
				}
			}
			
			
			return $return_array;
		}
		
		public function __toString() {
			return "[ObjectUserRelationDirection]";
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\ObjectUserRelationDirection<br />");
		}
	}
?>