<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\ItemInProcess
	class ItemInProcess {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("ItemInProcess::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$processes = $post->get_outgoing_direction()->get_type('following')->get_object_ids('process');
			$encoded_data->data['processes'] = $wprr_data_api->range()->encode_objects_as($processes, 'process');
			
			$parts = $post->get_outgoing_direction()->get_type('started')->get_object_ids('process-part');
			$encoded_data->data['started'] = $wprr_data_api->range()->encode_objects_as($parts, 'processPart');
			
			$parts = $post->get_outgoing_direction()->get_type('completed')->get_object_ids('process-part');
			$encoded_data->data['completed'] = $wprr_data_api->range()->encode_objects_as($parts, 'processPart');
			
			$parts = $post->get_outgoing_direction()->get_type('skipped')->get_object_ids('process-part');
			$encoded_data->data['skipped'] = $wprr_data_api->range()->encode_objects_as($parts, 'processPart');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ItemInProcess<br />");
		}
	}
?>