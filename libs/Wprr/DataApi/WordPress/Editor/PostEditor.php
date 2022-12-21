<?php
	namespace Wprr\DataApi\WordPress\Editor;

	// \Wprr\DataApi\WordPress\Post\Editor
	class PostEditor {
		
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
		
		public function add_term_by_id($term_id) {
			global $wprr_data_api;
			
			$wprr_data_api->performance()->start_meassure('PostEditor::add_term_by_id');
			
			$db = $wprr_data_api->database();
			
			$fields = array(
				'object_id' => $this->get_id(),
				'term_taxonomy_id' => $term_id,
			);
			
			$insert_statement = $this->get_insert_statement($fields);
			
			$query = 'INSERT INTO '.DB_TABLE_PREFIX.'term_relationships '.$insert_statement;
			
			$id = $db->insert($query);
			
			$wprr_data_api->performance()->stop_meassure('PostEditor::add_term_by_id');
			
			return null;
		}
		
		public function add_meta($key, $value) {
			global $wprr_data_api;
			
			$wprr_data_api->performance()->start_meassure('PostEditor::add_meta');
			
			$db = $wprr_data_api->database();
			
			if ( is_array( $value ) || is_object( $value ) ) {
				$value = serialize( $value );
			}
			
			$fields = array(
				'post_id' => $this->get_id(),
				'meta_key' => $key,
				'meta_value' => $value,
			);
			
			$insert_statement = $this->get_insert_statement($fields);
			
			$query = 'INSERT INTO '.DB_TABLE_PREFIX.'postmeta '.$insert_statement;
			
			$id = $db->insert($query);
			
			$wprr_data_api->performance()->stop_meassure('PostEditor::add_meta');
			
			return null;
		}
		
		public function get_insert_statement($fields) {
			
			global $wprr_data_api;
			
			$db = $wprr_data_api->database();
			
			$keys= array();
			$values = array();
			
			foreach($fields as $key => $value) {
				$keys[] = $key;
				
				if($value !== null) {
					$values[] = "'".$db->escape($value)."'";
				}
				else {
					$values[] = "null";
				}
			}
			
			return '('.implode(",", $keys).') VALUES ('.implode(",", $values).')';
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\PostEditor<br />");
		}
	}
?>