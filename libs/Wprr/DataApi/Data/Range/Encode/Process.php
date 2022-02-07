<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Process
	class Process {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("Process::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['name'] = $post->get_meta('name');
			
			$parts = $post->get_incoming_direction()->get_type('in')->get_object_ids_in_order('process-part', 'parts');
			$encoded_data->data['parts'] = $wprr_data_api->range()->encode_objects_as($parts, 'processPart');
			
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Process<br />");
		}
	}
?>