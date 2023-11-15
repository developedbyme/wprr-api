<?php
	namespace Wprr\DataApi\Data\Range\DataFunction;

	// \Wprr\DataApi\Data\Range\DataFunction\Cart
	class Cart {

		function __construct() {
			
		}
		
		public function get_data($data) {
			//var_dump("Cart::encode");
			
			global $wprr_data_api;
			
			$encoded_data = $wprr_data_api->range()->get_encoded_object('data');
			
			$encoded_data->data['cart'] = 'This is an example of the call';
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Cart<br />");
		}
	}
?>