<?php
	namespace Wprr;
	
	// \Wprr\ApiActionHooks
	class ApiActionHooks {

		function __construct() {
			//echo("\Wprr\ApiActionHooks::__construct<br />");
			
		}

		public function register() {
			//echo("\Wprr\ApiActionHooks::register<br />");

			add_action('wprr/api_action/woocommerce/add-to-cart', array($this, 'hook_woocommerce_add_to_cart'), 10, 2);
			add_action('wprr/api_action/woocommerce/apply-dicount-code', array($this, 'hook_woocommerce_apply_discount_code'), 10, 2);
			add_action('wprr/api_action/woocommerce/checkout', array($this, 'hook_woocommerce_checkout'), 10, 2);
			add_action('wprr/api_action/woocommerce/empty-cart', array($this, 'hook_woocommerce_empty_cart'), 10, 2);

		}

		public function hook_woocommerce_add_to_cart($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_woocommerce_add_to_cart<br />");
			
			WC()->cart->set_session();
			
			$product_id = $data['id'];
			$quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
			$variation_id = 0;
			$variation_data = array();
			$item_data = array();
			if(isset($data['itemData'])) {
				$item_data = $data['itemData'];
			}
			
			$add_to_cart = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation_data, $item_data);
			
			$response_data['addedToCart'] = $add_to_cart;
		}
		
		public function hook_woocommerce_apply_discount_code($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_woocommerce_apply_discount_code<br />");
			
			WC()->cart->set_session();
			
			$codes = explode(',', $data['code']);
			
			$results = array();
			
			foreach($codes as $code) {
				$result = WC()->cart->add_discount( sanitize_text_field( $code ) );
				$results[] = array('code' => $code, 'result' => $result);
			}
			
			$response_data['results'] = $results;
		}
		
		public function hook_woocommerce_checkout($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_woocommerce_checkout<br />");
			
			$cart = WC()->cart;
			$order_id = WC()->checkout()->create_order(array('customer_id' => get_current_user_id()));
			$order = wc_get_order( $order_id );
			//update_post_meta($order_id, '_customer_user', get_current_user_id());
			$order->calculate_totals();
			$response_data['order_id'] = $order_id;
			
			//Empty cart
			WC()->cart->empty_cart();
		}
		
		public function hook_woocommerce_empty_cart($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_woocommerce_empty_cart<br />");
			
			WC()->cart->empty_cart();
		}
		
		public static function test_import() {
			echo("Imported \Wprr\ApiActionHooks<br />");
		}
	}
?>
