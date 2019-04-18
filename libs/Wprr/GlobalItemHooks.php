<?php
	namespace Wprr;
	
	use \WP_Query;
	
	// \Wprr\GlobalItemHooks
	class GlobalItemHooks {
		
		function __construct() {
			//echo("\Wprr\GlobalItemHooks::__construct<br />");
			
			
		}
		
		public function register() {
			//echo("\Wprr\GlobalItemHooks::register<br />");
			
			$prefix = WPRR_DOMAIN.'/global-item';
			
			add_filter($prefix.'/wpml/languages', array($this, 'filter_wpml_languages'), 10, 1);
			add_filter($prefix.'/woocommerce/cart', array($this, 'filter_woocommerce_cart'), 10, 1);
		}
		
		public function filter_wpml_languages($return_object) {
			//echo("\Wprr\GlobalItemHooks::filter_wpml_languages<br />");
			
			$languages = icl_get_languages('skip_missing=0');
			foreach($languages as $language) {
				$return_object[] = array(
					'code' => $language['code'],
					'name' => $language['native_name'],
					'translatedName' => $language['translated_name'],
					'homeUrl' => $language['url'],
					'flagUrl' => $language['country_flag_url']
				);
			}
			
			return $return_object;
		}
		
		protected function add_cart_data($cart, &$return_object) {
			
			$encoded_items = array();
			
			$return_object['coupons'] = $cart->get_applied_coupons();
			$return_object['totals'] = $cart->get_totals();
			$return_object['currency'] = get_option('woocommerce_currency');
			
			$items = $cart->get_cart();
			foreach($items as $key => $cart_item) {
				
				$encoded_item = array(
					'key' => $key,
					'quantity' => $cart_item['quantity'],
					'product' => mrouter_encode_post_link($cart_item['product_id']),
					'total' => $cart_item['line_total']
				);
				
				$encoded_item = apply_filters(WPRR_DOMAIN.'/global-item/woocommerce/cart/meta', $encoded_item, $key, $cart_item);
				
				$encoded_items[] = $encoded_item;
			}
					
			$return_object['items'] = $encoded_items;
			
		}
		
		public function filter_woocommerce_cart($return_object) {
			//echo("\Wprr\GlobalItemHooks::filter_woocommerce_cart<br />");
			
			global $woocommerce;
			
			wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );
			
			$cart = $woocommerce->cart;
			
			$this->add_cart_data($cart, $return_object);
			
			$recurring_total = \WC_Subscriptions_Cart::calculate_subscription_totals(0, $woocommerce->cart);
			
			if($woocommerce->cart->recurring_carts) {
				$encoded_recurring_carts = array();
				
				foreach($woocommerce->cart->recurring_carts as $key => $recurring_cart) {
					$current_encoded_cart = array();
					$this->add_cart_data($recurring_cart, $current_encoded_cart);
					$encoded_recurring_carts[] = array(
						'key' => $key,
						'cart' => $current_encoded_cart,
						'nextPayment' => $recurring_cart->next_payment_date
					);
				}
				
				$return_object['recurring'] = $encoded_recurring_carts;
			}
			
			return $return_object;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\GlobalItemHooks<br />");
		}
	}
?>