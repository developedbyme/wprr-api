<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\ImagesFor
	class ImagesFor {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("ImagesFor::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['images'] = $wprr_data_api->range()->encode_objects_as($post->get_incoming_direction()->get_type('for')->get_object_ids('image'), 'image');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ImagesFor<br />");
		}
	}
?>