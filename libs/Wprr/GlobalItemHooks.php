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
			add_filter($prefix.'/woocommerce/current-customer', array($this, 'filter_woocommerce_current_customer'), 10, 1);
			add_filter($prefix.'/woocommerce/customer', array($this, 'filter_woocommerce_customer'), 10, 3);
			
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
			
			\Wprr\OddCore\Utils\WoocommerceFunctions::ensure_wc_has_cart();
			
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
		
		public function filter_woocommerce_current_customer($return_object) {
			
			\Wprr\OddCore\Utils\WoocommerceFunctions::ensure_wc_has_cart();
			
			$customer = WC()->customer;
			
			$current_data = $customer->get_data();
			
			$return_object['id'] = $customer->get_id();
			$return_object['user'] = wprr_encode_user(get_user_by('id', $customer->get_id()));
			$return_object['isPayingCustomer'] = $current_data['is_paying_customer'];
			$return_object['contactDetails'] = array('billing' => $current_data['billing'], 'shipping' => $current_data['shipping']);
			
			return $return_object;
		}
		
		public function filter_woocommerce_customer($return_object, $item, $data) {
			
			$can_get_private_data = apply_filters(WPRR_DOMAIN.'/current_user_can_get_private_data', current_user_can('edit_others_posts'), $data);
			if(!$can_get_private_data) {
				throw(new \Exception("User doesn't have persmission"));
			}
			
			$user_id = (int)$data['id'];
			$customer = new \WC_Customer($user_id);
			
			$current_data = $customer->get_data();
			
			$encoder = wprr_get_encoder();
			
			$return_object['id'] = $current_data['id'];
			$return_object['user'] = $encoder->encode_user_with_private_data(get_user_by('id', $user_id));
			$return_object['isPayingCustomer'] = $current_data['is_paying_customer'];
			$return_object['contactDetails'] = array('billing' => $current_data['billing'], 'shipping' => $current_data['shipping']);
			
			return $return_object;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\GlobalItemHooks<br />");
		}
	}
?>