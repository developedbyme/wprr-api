<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Type
	class Type {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Type::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['identifier'] = $post->get_meta('identifier');
			$encoded_data->data['name'] = $post->get_meta('name');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Type<br />");
		}
	}
?>