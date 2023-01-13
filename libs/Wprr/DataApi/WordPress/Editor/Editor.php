<?php
	namespace Wprr\DataApi\WordPress\Editor;

	// \Wprr\DataApi\WordPress\WordPress\Editor
	class Editor {
		
		protected $_posts = array();
		
		function __construct() {
			
		}
		
		public function get_post_editor($id) {
			if(!isset($this->_posts[$id])) {
				$new_post = new \Wprr\DataApi\WordPress\Editor\PostEditor();
				$new_post->setup($id);
				$this->_posts[$id] = $new_post;
			}
			
			return $this->_posts[$id];
		}
		
		public function create_post($type, $title, $parent = 0) {
			global $wprr_data_api;
			
			$wprr_data_api->performance()->start_meassure('Editor::create_post');
			
			$db = $wprr_data_api->database();
			
			$date = date('Y-m-d H:i:s');
			$gmt_date = gmdate('Y-m-d H:i:s');
			
			$fields = array(
				'post_type' => $type,
				'post_title' => $title,
				'post_excerpt' => '',
				'post_content' => '',
				'post_parent' => $parent,
				'post_date' => $date,
				'post_date_gmt' => $gmt_date,
				'post_modified' => $date,
				'post_modified_gmt' => $gmt_date,
				'post_status' => 'draft',
				'to_ping' => '',
				'pinged' => '',
				'post_content_filtered' => ''
			);
			
			$insert_statement = $this->get_insert_statement($fields);
			
			$query = 'INSERT INTO '.DB_TABLE_PREFIX.'posts '.$insert_statement;
			
			$id = $db->insert($query);
			
			$wprr_data_api->performance()->stop_meassure('Editor::create_post');
			
			if($id) {
				return $wprr_data_api->wordpress()->get_post($id);
			}
			
			throw(new \Exception('Could not create post'));
			return null;
		}
		
		public function create_relation($from_post, $to_post, $type_term, $start_time = -1) {
			global $wprr_data_api;
			
			$relation_post = $this->create_post('dbm_object_relation', $from_post->get_id().' '.$type_term->get_slug().' '.$to_post->get_id());
			
			$editor = $relation_post->editor();
			
			$editor->add_term($wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term('object-relation'));
			$editor->add_term($type_term);
			
			$editor->add_meta('startAt', $start_time);
			$editor->add_meta('endAt', -1);
			
			$editor->add_meta('fromId', $from_post->get_id());
			$editor->add_meta('toId', $to_post->get_id());
			
			return $relation_post;
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
			echo("Imported \Wprr\DataApi\WordPress\Editor<br />");
		}
	}
?>