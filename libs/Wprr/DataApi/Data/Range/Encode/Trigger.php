<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Trigger
	class Trigger {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Trigger::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['type'] = $wprr_data_api->range()->encode_object_as($post->get_incoming_direction()->get_type('for')->get_single_object_id('type/trigger-type'), 'type');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Trigger<br />");
		}
	}
?>