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
			
			$cart = $wprr_data_api->wordpress()->woocommerce()->get_cart();
			
			$product_ids = array_map(function($item) {
				return $item->get_id();
			}, $cart->get_products());
			
			$wprr_data_api->range()->encode_objects_as($product_ids, 'product');
			
			$encoded_line_items = array();
			
			foreach($cart->get_line_items() as $key => $line_item) {
				$current_encoded_line_item = array();
				$current_encoded_line_item['key'] = $key;
				$current_encoded_line_item['product'] = $line_item->get_product()->get_id();
				$current_encoded_line_item['quantity'] = $line_item->get_quantity();
				
				$encoded_line_items[] = $current_encoded_line_item;
			}
			
			$encoded_data->data['lineItems'] = $encoded_line_items;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Cart<br />");
		}
	}
?>