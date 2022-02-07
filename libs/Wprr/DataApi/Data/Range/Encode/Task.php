<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Task
	class Task {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("Task::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$type = $post->get_incoming_direction()->get_type('for')->get_single_object_id('type/task-type');
			$encoded_data->data['type'] = $wprr_data_api->range()->encode_object_as($type, 'type');
			
			$type = $post->get_incoming_direction()->get_type('for')->get_single_object_id('type/task-status');
			$encoded_data->data['status'] = $wprr_data_api->range()->encode_object_as($type, 'type');
			
			$wprr_data_api->range()->encode_object_as($id, 'itemInProcess');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Task<br />");
		}
	}
?>