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
			add_filter($prefix.'/woocommerce/gateways', array($this, 'filter_woocommerce_gateways'), 10, 1);
			
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
		
		public function filter_woocommerce_cart($return_object) {
			//echo("\Wprr\GlobalItemHooks::filter_woocommerce_cart<br />");
			
			global $woocommerce;
			
			$cart = $woocommerce->cart;
			
			$encoded_items = array();
			
			$return_object['coupons'] = $cart->get_applied_coupons();
			$return_object['totals'] = $cart->get_totals();
			
			$items = $cart->get_cart();
			foreach($items as $key => $cart_item) {
				
				$encoded_item = array(
					'key' => $key,
					'quantity' => $cart_item['quantity'],
					'product' => mrouter_encode_post_link($cart_item['product_id']),
					'total' => $cart_item['line_total']
				);
				
				$encoded_items[] = $encoded_item;
			}
					
			$return_object['items'] = $encoded_items;
			
			return $return_object;
		}
		
		public function filter_woocommerce_gateways($return_object) {
			$gateways = WC()->payment_gateways->get_available_payment_gateways();
			
			$encoded_gateways = array();
			
			foreach($gateways as $id => $gateway) {
				$encoded_gateways[] = array(
					'id' => $id,
					'title' => $gateway->get_title(),
					'description' => $gateway->get_description()
				);
			}
			
			return $encoded_gateways;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\GlobalItemHooks<br />");
		}
	}
?>