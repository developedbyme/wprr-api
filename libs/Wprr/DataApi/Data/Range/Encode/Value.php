<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Value
	class Value {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Value::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['value'] = $post->get_meta('value');
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Value<br />");
		}
	}
?>