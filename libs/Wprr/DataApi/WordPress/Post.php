<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\Post
	class Post {
		
		protected $_id = 0;
		protected $_database_data = null;
		protected $_database_meta = null;
		protected $_meta = array();
		protected $_database_taxonomy_terms = null;
		
		function __construct() {
			
		}
		
		public function setup($id) {
			$this->_id = $id;
			
			return $this;
		}
		
		public function get_database_data() {
			if(!$this->_database_data) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$query = 'SELECT * FROM wp_posts WHERE ID = "'.$this->_id.'"';
				$this->_database_data = $db->query_first($query);
			}
			
			return $this->_database_data;
		}
		
		public function get_database_meta_data() {
			if(!$this->_database_meta) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$query = 'SELECT meta_key, meta_value FROM wp_postmeta WHERE post_id = "'.$this->_id.'"';
				$this->_database_meta = $db->query($query);
			}
			
			return $this->_database_meta;
		}
		
		public function get_post_title() {
			
			$data = $this->get_database_data();
			
			return $data['post_title'];
		}
		
		public function get_meta($name) {
			if(!isset($this->_meta[$name])) {
				$meta_data = $this->get_database_meta_data();
				
				$selected_meta = array();
				
				foreach($meta_data as $meta_data_row) {
					if($meta_data_row['meta_key'] === $name) {
						$selected_meta[] = $meta_data_row['meta_value'];
					}
				}
				
				$this->_meta[$name] = $selected_meta;
			}
			
			return $this->_meta[$name][0];
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Post<br />");
		}
	}
?>