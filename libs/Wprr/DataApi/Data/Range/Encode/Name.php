<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Name
	class Name {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Name::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['name'] = $post->get_meta('name');
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Name<br />");
		}
	}
?>