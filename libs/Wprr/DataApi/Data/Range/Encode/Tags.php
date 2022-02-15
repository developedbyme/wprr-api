<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Tags
	class Tags {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Tags::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['tags'] = $wprr_data_api->range()->encode_objects_as($post->get_incoming_direction()->get_type('for')->get_object_ids('type/tag'), 'type');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Tags<br />");
		}
	}
?>