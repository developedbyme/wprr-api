<?php
	namespace Wprr\DataApi\WordPress\WooCommerce\Cart;

	// \Wprr\DataApi\WordPress\WordPress\WooCommerce\Cart
	class LineItem {
		
		protected $_quantity;
		protected $_product;
		
		function __construct() {
			
		}
		
		public function setup($quantity, $product) {
			
			$this->_quantity = $quantity;
			$this->_product = $product;
			
			return $this;
		}
		
		public function get_product() {
			return $this->_product;
		}
		
		public function get_quantity() {
			return $this->_quantity;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\WordPress\WooCommerce\Cart\LineItem<br />");
		}
	}
?>