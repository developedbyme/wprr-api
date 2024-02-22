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
			
			//METODO: update custom tables
			
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
		
		public function get_or_create_type($type, $identifier) {
			global $wprr_data_api;
			
			$query = $wprr_data_api->database()->new_select_query();
			
			$specific_type_term = $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term($type);
			$ids = $query->set_post_type('dbm_data')->include_private()->include_only($specific_type_term->get_ids())->meta_query('identifier', $identifier)->get_ids_without_storage();
			
			if(!empty($ids)) {
				return $wprr_data_api->wordpress()->get_post($ids[0]);
			}
			
			$post = $this->create_post('dbm_data', $identifier);
			
			$type_term = $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term('type');
			$post->editor()->add_term($type_term);
			$post->editor()->add_term($specific_type_term);
			$post->editor()->update_meta('identifier', $identifier);
			$post->editor()->update_meta('name', $identifier);
			$post->editor()->change_status('private');
			
			$type_term->invalidate();
			$specific_type_term->invalidate();
			
			return $post;
		}
		
		public function create_media($path, $parent = 0) {
			$post = $this->create_post("attachment", $path, $parent);
			
			$post->editor()->change_status('inherit');
			
			$url = UPLOAD_URL.'/'.$path;
			
			$post->editor()->add_meta('_wp_attached_file', $path);
			
			$imagesize = getimagesize(UPLOAD_DIR.'/'.$path);
			$post->editor()->update_field('post_mime_type', $imagesize["mime"]);
			
			$image_meta = array(
				'width' => $imagesize[0],
				'height' => $imagesize[1],
				'file' => $path,
				'filesize' => filesize( UPLOAD_DIR.'/'.$path ),
				'image_meta' => array(
					'aperture' => "0",
					'camera' => "",
					'caption' => "",
					'copyright' => "",
					'created_timestamp' => "0",
					'credit' => "",
					'focal_length' => "0",
					'iso' => "0",
					'keywords' => array(),
					'orientation' => "0",
					'shutter_speed' => "0",
					'title' => "",
				),
				'sizes' => array(
					'full' => array(
						'file' => basename($path),
						'width' => $imagesize[0],
						'height' => $imagesize[1],
						'mime-type' => $imagesize["mime"]
					)
				),
			);
			
			$post->editor()->add_meta('_wp_attachment_metadata', $image_meta);
			
			if($parent) {
				$post->editor()->add_meta('_wpml_media_usage', array('posts' => $parent));
			}
			
			return $post;
		}
		
		public function add_action_to_process($type, $from_ids = null, $data = null, $time = null) {
			$action_type = $this->get_or_create_type('type/action-type', $type);
		
			$action = $this->create_post('dbm_data', 'Action: '.$type);
			
			$action->editor()->add_term_by_path('dbm_type', 'action');
			
			if($data) {
				$action->editor()->add_term_by_path('dbm_type', 'value-item');
				$action->editor()->add_meta('value', $data);
			}
		
			if($from_ids) {
				if(!is_array($from_ids)) {
					$from_ids = array($from_ids);
				}
			
				foreach($from_ids as $from_id) {
					$action->editor()->add_outgoing_relation_by_name(wprr_get_data_api()->wordpress()->get_post($from_id), 'from');
				}
			}
			
			$action->editor()->add_incoming_relation_by_name($action_type, 'for');
			
			$action->editor()->make_private();
		
			$action->editor()->add_meta('needsToProcess', true);
			$status = $this->get_or_create_type('type/action-status', 'readyToProcess');
		
			if(!$time) {
				$time = time();
			}
		
			$action->editor()->add_incoming_relation_by_name($status, 'for', $time);
			
			return $action;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\WordPress\Editor<br />");
		}
	}
?>