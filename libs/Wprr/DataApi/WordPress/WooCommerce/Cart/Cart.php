<?php
	namespace Wprr\DataApi\WordPress\WooCommerce\Cart;

	// \Wprr\DataApi\WordPress\WordPress\WooCommerce\Cart
	class Cart {
		
		protected $_line_items = array();
		
		function __construct() {
			
		}
		
		public function setup_from_session_data($session_data) {
			
			global $wprr_data_api;
			$wp = $wprr_data_api->wordpress();
			
			$session_line_items = unserialize($session_data['cart']);
			
			foreach($session_line_items as $key => $value) {
				
				$line_item = new \Wprr\DataApi\WordPress\WooCommerce\Cart\LineItem();
				$product = $wp->get_post($value['product_id']);
				
				$line_item->setup($value['quantity'], $product);
				
				$this->_line_items[$key] = $line_item;
			}
			
			return $this;
		}
		
		public function get_products() {
			return array_map(function($item) {
				return $item->get_product();
			}, $this->_line_items);
		}
		
		public function get_line_items() {
			return $this->_line_items;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\WordPress\WooCommerce\Cart\Cart<br />");
		}
	}
?>