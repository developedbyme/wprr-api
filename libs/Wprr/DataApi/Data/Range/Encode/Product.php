<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Product
	class Product {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Product::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$wprr_data_api->range()->encode_object_as($id, 'postTitle');
			
			$encoded_data->data['price'] = (float)$post->get_meta('_price');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Product<br />");
		}
	}
?>