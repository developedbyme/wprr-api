<?php
	namespace Wprr\OddCore\Utils;
	
	// \Wprr\OddCore\Utils\WoocommerceFunctions
	class WoocommerceFunctions {
		
		public static function ensure_wc_has_cart() {
			//echo('\Wprr\OddCore\Utils\WoocommerceFunctions::ensure_wc_has_cart');
			
			include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
			include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
			wc_load_cart();
			
			/*
			if ( null === WC()->cart ) {
				if ( defined( 'WC_ABSPATH' ) ) {
					// WC 3.6+ - Cart and notice functions are not included during a REST request.
					include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
					include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
				}
				
				global $woocommerce, $sitepress, $woocommerce_wpml;
				

				if ( null === WC()->session ) {
					$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );

					//Prefix session class with global namespace if not already namespaced
					if ( false === strpos( $session_class, '\\' ) ) {
						$session_class = '\\' . $session_class;
					}
					
					WC()->session = new $session_class();
					WC()->session->init();
					
					WC()->session->set_customer_session_cookie(true);
				}

				if ( null === WC()->customer ) {
					WC()->customer = new \WC_Customer( get_current_user_id(), true );
				}

				if ( null === WC()->cart ) {
					WC()->cart = new \WC_Cart();

					// We need to force a refresh of the cart contents from session here (cart contents are normally refreshed on wp_loaded, which has already happened by this point).
					WC()->cart->get_cart();
				}
			}
			*/
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Utils\WoocommerceFunctions<br />");
		}
	}
?>