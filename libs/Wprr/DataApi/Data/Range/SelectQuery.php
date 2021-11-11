<?php
	namespace Wprr\DataApi\Data\Range;

	// \Wprr\DataApi\Data\Range\SelectQuery
	class SelectQuery {
		
		protected $_only = null;
		protected $_statuses = array('publish');
		protected $_post_types = null;

		function __construct() {
			
		}
		
		public function set_post_type($post_type) {
			$this->_post_types = array($post_type);
			
			return $this;
		}
		
		public function include_private() {
			$this->_statuses[] = 'private';
			
			return $this;
		}
		
		public function meta_query($field, $value) {
			
			global $wprr_data_api;
			$db = $wprr_data_api->database();
			
			$query = 'SELECT post_id as id FROM wp_postmeta WHERE meta_key = "'.$db->escape($field).'" AND meta_value = "'.$db->escape($value).'"';
			
			$posts = $db->query($query);
		
			$ids = array_map(function($item) {
				return (int)$item['id'];
			}, $posts);
			
			$this->include_only($ids);
			
			return $this;
		}
		
		public function include_term($term) {
			if(!$term) {
				//METODO: error message
				$this->include_only(array());
				return $this;
			}
			
			$this->include_only($term->get_ids());
			
			return $this;
		}
		
		public function include_term_by_path($taxonomy, $term_path) {
			global $wprr_data_api;
			$term = $wprr_data_api->wordpress()->get_taxonomy($taxonomy)->get_term($term_path);
			
			$this->include_term($term);
			
			return $this;
		}
		
		public function include_only($ids) {
			if(!$ids || empty($ids)) {
				$this->_only = array();
			}
			else {
				if($this->_only !== null) {
					global $wprr_data_api;
					
					$wprr_data_api->performance()->start_meassure('SelectQuery::include_only array_intersect');
					$array1 = array_flip($this->_only);
					$array2 = array_flip($ids);
					
					$intersected_array = array_intersect_key($array1, $array2);
					
					$this->_only = array_flip($intersected_array);
					
					//$intersected_array = array_intersect($this->_only, $ids);
					$wprr_data_api->performance()->stop_meassure('SelectQuery::include_only array_intersect');
				}
				else {
					$this->_only = $ids;
				}
			}
			
			return $this;
		}
		
		public function include_none() {
			$this->_only = array();
			
			return $this;
		}
		
		public function include_post_relation_by_path($post, $relation) {
			global $wprr_data_api;
			
			$parent_term = $wprr_data_api->wordpress()->get_taxonomy('dbm_relation')->get_term($relation);
			
			if(!$parent_term) {
				$wprr_data_api->output()->log('No relation named '.$relation);
				$this->include_none();
				return $this;
			}
			
			$term = $post->get_single_term_in($parent_term);
			
			if(!$term) {
				$wprr_data_api->output()->log('Post '.$post->get_id().' doesn\'t have any relation in group '.$relation);
				$this->include_none();
				return $this;
			}
			
			$this->include_term($term);
			
			return $this;
		}
		
		public function get_query() {
			global $wprr_data_api;
			$db = $wprr_data_api->database();
			
			$has_query = false;
			$query = "SELECT ID as id FROM wp_posts";
			
			$where = array();
			
			if($this->_statuses) {
				$has_query = true;
				$encoded_statuses = array();
				
				foreach($this->_statuses as $item) {
					$encoded_statuses[] = '"'.$db->escape($item).'"';
				}
				
				$where[] = 'post_status in ('.implode(',', $encoded_statuses).')';
			}
			
			if($this->_post_types) {
				$has_query = true;
				$encoded_types = array();
				
				foreach($this->_post_types as $item) {
					$encoded_types[] = '"'.$db->escape($item).'"';
				}
				
				$where[] = 'post_type in ('.implode(',', $encoded_types).')';
			}
			
			if($this->_only !== null) {
				$where[] = 'ID in ('.implode(',', $this->_only).')';
			}
			
			$query .= " WHERE ".implode(' AND ', $where);
			
			return $query;
		}
		
		public function get_ids() {
			if($this->_only !== null && empty($this->_only)) {
				return array();
			}
			
			global $wprr_data_api;
			$db = $wprr_data_api->database();
			$query = $this->get_query();
			
			$posts = $db->query($query);
			
			return array_map(function($item) {
				return (int)$item['id'];
			}, $posts);
		}
		
		public function get_id() {
			if($this->_only !== null && empty($this->_only)) {
				return 0;
			}
			
			global $wprr_data_api;
			$db = $wprr_data_api->database();
			$query = $this->get_query().' LIMIT 1';
			
			$posts = $db->query($query);
			
			if(!empty($posts)) {
				return (int)$posts[0]['id'];
			}
			
			return 0;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\SelectQuery<br />");
		}
	}
?>