<?php
	namespace Wprr\DataApi\WordPress\Editor;

	class UserEditor {
		
		protected $_id = 0;
		
		function __construct() {
			
		}
		
		public function setup($id) {
			$this->_id = $id;
			
			return $this;
		}
		
		public function get_id() {
			return $this->_id;
		}
		
		public function user() {
			global $wprr_data_api;
			return $wprr_data_api->wordpress()->get_user($this->get_id());
		}
		
		public function update_field($field, $value) {
			global $wprr_data_api;
			$db = $wprr_data_api->database();
			
			$query = 'UPDATE '.DB_TABLE_PREFIX.'users SET '.$db->escape($field).' = \''.$db->escape($value).'\' WHERE id = '.$this->get_id();
			
			$result = $db->update($query);
			
			$this->user()->invalidate_user_data();
			
			return $this;
		}

		public function add_meta($key, $value) {
			global $wprr_data_api;
			
			$wprr_data_api->performance()->start_meassure('UserEditor::add_meta');
			
			$db = $wprr_data_api->database();
			
			if ( is_array( $value ) || is_object( $value ) ) {
				$value = serialize( $value );
			}
			
			$fields = array(
				'user_id' => $this->get_id(),
				'meta_key' => $key,
				'meta_value' => $value,
			);
			
			$insert_statement = $this->get_insert_statement($fields);
			
			$query = 'INSERT INTO '.DB_TABLE_PREFIX.'usermeta '.$insert_statement;
			
			$id = $db->insert($query);
			
			$wprr_data_api->performance()->stop_meassure('UserEditor::add_meta');
			
			$this->user()->invalidate_meta();
			
			return $this;
		}
		
		public function update_meta($key, $value) {
			
			global $wprr_data_api;
			$db = $wprr_data_api->database();
			
			$query = 'SELECT umeta_id as id FROM '.DB_TABLE_PREFIX.'usermeta WHERE user_id = \''.$db->escape($this->get_id()).'\' AND meta_key = \''.$db->escape($key).'\'';
			
			$results = $db->query_without_storage($query);
			
			$ids = array_map(function($item) {return (int)$item['id'];}, $results);
			
			if(!empty($ids)) {
				$first_id = array_shift($ids);
				
				if ( is_array( $value ) || is_object( $value ) ) {
					$value = serialize( $value );
				}
				
				$query = 'UPDATE '.DB_TABLE_PREFIX.'usermeta SET meta_value = \''.$db->escape($value).'\' WHERE umeta_id = '.$first_id;
			
				$result = $db->update($query);
				
				if(!empty($ids)) {
					$query = 'DELETE FROM '.DB_TABLE_PREFIX.'usermeta WHERE umeta_id IN ('.implode(',', $ids).')';
			
					$result = $db->update($query);
				}
				$this->user()->invalidate_meta();
			}
			else {
				$this->add_meta($key, $value);
			}
			
			return $this;
		}
		
		public function delete_meta($key) {
			
			global $wprr_data_api;
			$db = $wprr_data_api->database();
			
			$query = 'DELETE FROM '.DB_TABLE_PREFIX.'usermeta WHERE user_id = \''.$db->escape($this->get_id()).'\' AND meta_key = \''.$db->escape($key).'\'';
			
			$result = $db->update($query);
			
			$this->user()->invalidate_meta();
			
			return $this;
		}

		public function change_email_login($email) {
			global $wprr_data_api;
			$existing_email = $wprr_data_api->wordpress()->get_user_by_email($email);
			$existing_login = $wprr_data_api->wordpress()->get_user_by_login($email);

			if($existing_email || $existing_login) {
				return false;
			}

			$this->update_field('user_login', $email);
			$this->update_field('user_email', $email);

			return true;
		}

		public function change_email($email) {
			global $wprr_data_api;
			$existing_email = $wprr_data_api->wordpress()->get_user_by_email($email);

			if($existing_email) {
				return false;
			}

			$this->update_field('user_email', $email);

			return true;
		}

		public function change_username_to_id() {
			$id = $this->get_id();
			$this->update_field('user_login', 'user'.$id);
			$this->update_field('user_nicename', 'user'.$id);
		}
	}
?>