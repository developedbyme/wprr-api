<?php
	namespace Wprr\OddCore\Utils;
	
	// \Wprr\OddCore\Utils\WoocommerceFunctions
	class WoocommerceFunctions {
		
		public static function ensure_wc_has_cart() {
			//echo('\Wprr\OddCore\Utils\WoocommerceFunctions::ensure_wc_has_cart');
			
			include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
			include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
			wc_load_cart();
			WC()->cart->get_cart();
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Utils\WoocommerceFunctions<br />");
		}
	}
?>