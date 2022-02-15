<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Identifier
	class Identifier {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Identifier::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['identifier'] = $post->get_meta('identifier');
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Identifier<br />");
		}
	}
?>