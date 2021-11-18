<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\User
	class User {
		
		protected $_id = 0;
		protected $_database_data = null;
		protected $_database_meta = null;
		protected $_meta = array();
		
		function __construct() {
			
		}
		
		public function setup($id) {
			$this->_id = $id;
			
			return $this;
		}
		
		public function get_id() {
			return $this->_id;
		}
		
		public function get_database_data() {
			if(!$this->_database_data) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$query = 'SELECT * FROM wp_users WHERE ID = "'.$this->_id.'"';
				$this->_database_data = $db->query_first($query);
			}
			
			return $this->_database_data;
		}
		
		public function get_database_meta_data() {
			if(!$this->_database_meta) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$query = 'SELECT meta_key, meta_value FROM wp_usermeta WHERE post_id = "'.$this->_id.'"';
				$this->_database_meta = $db->query($query);
			}
			
			return $this->_database_meta;
		}
		
		public function get_data($field) {
			$data = $this->get_database_data();
			
			return $data[$field];
		}
		
		public function get_meta($name) {
			if(!isset($this->_meta[$name])) {
				$meta_data = $this->get_database_meta_data();
				
				$selected_meta = array();
				
				foreach($meta_data as $meta_data_row) {
					if($meta_data_row['meta_key'] === $name) {
						$value = $meta_data_row['meta_value'];
						
						if(isset($value[1]) && $value[1] === ":") {
							$unserialize_value = unserialize($value);
							if($unserialize_value !== false) {
								$value = $unserialize_value;
							}
						}
						
						$selected_meta[] = $value;
					}
				}
				
				$this->_meta[$name] = $selected_meta;
			}
			
			return $this->_meta[$name][0];
		}
		
		public function get_display_name() {
			return $this->get_data('display_name');
		}
		
		public function get_email() {
			return $this->get_data('user_email');
		}
		
		public function get_gravatar_hash() {
			return md5( strtolower( trim( $this->get_email() ) ) );
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\User<br />");
		}
	}
?>