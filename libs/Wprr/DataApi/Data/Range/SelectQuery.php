<?php
	namespace Wprr\DataApi\Data\Range;

	// \Wprr\DataApi\Data\Range\SelectQuery
	class SelectQuery {
		
		protected $_only = null;
		protected $_statuses = array('publish');
		protected $_post_types = null;

		function __construct() {
			
		}
		
		public function include_only($ids) {
			if(!$ids || empty($ids)) {
				$this->_only = array();
			}
			else {
				if($this->_only !== null) {
					$intersected_array = array_intersect($this->_only, $ids);
					
					$this->_only = $intersected_array;
				}
				else {
					$this->_only = $ids;
				}
			}
			
			return $this;
		}
		
		public function get_ids() {
			if($this->_only !== null && empty($this->_only)) {
				return array();
			}
			
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
			
			$posts = $db->query($query);
			
			return array_map(function($item) {
				return (int)$item['id'];
			}, $posts);
		}
		
		public function get_id() {
			
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\SelectQuery<br />");
		}
	}
?>