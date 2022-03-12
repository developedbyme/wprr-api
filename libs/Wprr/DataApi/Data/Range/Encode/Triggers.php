<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Triggers
	class Triggers {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Triggers::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['triggers'] = $wprr_data_api->range()->encode_objects_as($post->get_incoming_direction()->get_type('for')->get_object_ids('trigger'), 'trigger');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Triggers<br />");
		}
	}
?>