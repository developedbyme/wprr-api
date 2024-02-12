<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\User
	class User {
		
		protected $_id = 0;
		protected $_database_data = null;
		protected $_database_meta = null;
		protected $_meta = array();
		protected $_postRelations = null;
		
		function __construct() {
			
		}
		
		public function setup($id) {
			$this->_id = $id;
			
			return $this;
		}
		
		public function get_id() {
			return $this->_id;
		}
		
		public function is_trusted() {
			global $wprr_data_api;
			return $wprr_data_api->wordpress()->is_user_trusted($this);
		}
		
		public function get_database_data() {
			if(!$this->_database_data) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$query = 'SELECT * FROM '.DB_TABLE_PREFIX.'users WHERE ID = "'.$this->_id.'"';
				$this->_database_data = $db->query_first($query);
			}
			
			return $this->_database_data;
		}
		
		public function get_database_meta_data() {
			if(!$this->_database_meta) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$query = 'SELECT meta_key, meta_value FROM '.DB_TABLE_PREFIX.'usermeta WHERE user_id = "'.$this->_id.'"';
				$this->_database_meta = $db->query_without_storage($query);
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
		
		public function get_roles() {
			$capabilites = $this->get_meta(DB_TABLE_PREFIX.'capabilities');
			
			$return_array = array();
			foreach($capabilites as $name => $active) {
				if($active) {
					$return_array[] = $name;
				}
			}
			
			return $return_array;
		}
		
		public function get_post_relations() {
			if(!$this->_postRelations) {
				$this->_postRelations = new \Wprr\DataApi\WordPress\ObjectRelation\ObjectUserRelationFromUserDirection();
				$this->_postRelations->setup($this);
			}
			
			return $this->_postRelations;
		}
		
		public function get_relation_types() {
			return $this->get_post_relations()->get_types();
		}
		
		public function get_relation_type($type) {
			return $this->get_post_relations()->get_type($type);
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\User<br />");
		}
	}
?>