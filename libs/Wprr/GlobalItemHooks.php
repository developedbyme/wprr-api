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
			
			add_filter($prefix.'/wpml/languages', array($this, 'filter_wpml_languages'), 10, 3);
			add_filter($prefix.'/woocommerce/cart', array($this, 'filter_woocommerce_cart'), 10, 3);
			add_filter($prefix.'/woocommerce/gateways', array($this, 'filter_woocommerce_gateways'), 10, 1);
			add_filter($prefix.'/woocommerce/current-customer', array($this, 'filter_woocommerce_current_customer'), 10, 1);
			add_filter($prefix.'/woocommerce/customer', array($this, 'filter_woocommerce_customer'), 10, 3);
			add_filter($prefix.'/woocommerce/order-statuses', array($this, 'filter_woocommerce_order_statuses'), 10, 1);
			
			add_filter($prefix.'/shortcode', array($this, 'filter_shortcode'), 10, 3);
			add_filter($prefix.'/theme-mods', array($this, 'filter_theme_mods'), 10, 3);
			add_filter($prefix.'/acf/options', array($this, 'filter_acf_options'), 10, 3);
			add_filter($prefix.'/oembed', array($this, 'filter_oembed'), 10, 3);
			
			add_filter($prefix.'/development/generate-data-api-settings', array($this, 'filter_development_generate_data_api_settings'), 10, 3);
			add_filter($prefix.'/development/wprr-test', array($this, 'filter_development_wprr_test'), 10, 3);
		}
		
		public function filter_wpml_languages($return_object, $item, $data) {
			//echo("\Wprr\GlobalItemHooks::filter_wpml_languages<br />");
			
			$permalink = null;
			if(isset($data['page'])) {
				$permalink = $data['page'];
			}
			
			$languages = icl_get_languages('skip_missing=0');
			foreach($languages as $language) {
				
				$code = $language['code'];
				
				$encoded_object = array(
					'code' => $code,
					'name' => $language['native_name'],
					'translatedName' => $language['translated_name'],
					'homeUrl' => $language['url'],
					'flagUrl' => $language['country_flag_url']
				);
				
				if($permalink) {
					$encoded_object['pageUrl'] = apply_filters('wpml_permalink', $permalink, $code);
				}
				
				$return_object[] = $encoded_object;
			}
			
			return $return_object;
		}
		
		protected function add_cart_data($cart, &$return_object) {
			
			$encoded_items = array();
			
			$return_object['coupons'] = array_values($cart->get_applied_coupons());
			$return_object['totals'] = $cart->get_totals();
			$return_object['currency'] = get_woocommerce_currency();
			
			
			$items = $cart->get_cart();
			foreach($items as $key => $cart_item) {
				
				$product = $cart_item['data'];
				
				$encoded_item = array(
					'key' => $key,
					'quantity' => $cart_item['quantity'],
					'product' => mrouter_encode_post_link($cart_item['product_id']),
					'total' => $cart_item['line_total'],
					'regularPrice' => (float)$product->get_regular_price('raw')
				);
				
				$encoded_item = apply_filters(WPRR_DOMAIN.'/global-item/woocommerce/cart/meta', $encoded_item, $key, $cart_item);
				
				$encoded_items[] = $encoded_item;
			}
					
			$return_object['items'] = $encoded_items;
			
		}
		
		public function filter_woocommerce_cart($return_object, $item, $data) {
			//echo("\Wprr\GlobalItemHooks::filter_woocommerce_cart<br />");
			
			wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );
			//MENOTE: $woocommerce_wpml->multi_currency->set_client_currency requires a cart and ensure_wc_has_cart needs to have set_client_currency set, that is why it ensures twice
			\Wprr\OddCore\Utils\WoocommerceFunctions::ensure_wc_has_cart();
			
			global $woocommerce_wpml;
			if($woocommerce_wpml && isset($woocommerce_wpml->multi_currency)) {
				//MENOTE: get_client_currency is called before prepare_api_request and the currency is then cached, this will reset the cache and get the correct value the next time get_client_currency is called (within get_woocommerce_currency).
				$woocommerce_wpml->multi_currency->set_client_currency(null);
			}
			
			//MENOTE: $woocommerce_wpml->multi_currency->set_client_currency requires a cart and ensure_wc_has_cart needs to have set_client_currency set, that is why it ensures twice
			\Wprr\OddCore\Utils\WoocommerceFunctions::ensure_wc_has_cart();
			
			$cart = WC()->cart;
			
			$this->add_cart_data($cart, $return_object);
			
			if(class_exists("WC_Subscriptions_Cart")) {
				$recurring_total = \WC_Subscriptions_Cart::calculate_subscription_totals(0, $cart);
			
				if($cart->recurring_carts) {
					$encoded_recurring_carts = array();
				
					foreach($cart->recurring_carts as $key => $recurring_cart) {
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
			}
			
			if(isset($data["sessionVariables"])) {
				
				$session_data = array();
				
				$session_variable_names = explode(',', $data["sessionVariables"]);
				foreach($session_variable_names as $session_variable_name) {
					$session_data[$session_variable_name] = WC()->session->get($session_variable_name);
				}
				
				$return_object['session'] = $session_data;
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
		
		public function filter_woocommerce_order_statuses($return_object) {
			$statuses = wc_get_order_statuses();
			
			$encoded_statuses = array();
			
			foreach($statuses as $id => $name) {
				$encoded_statuses[] = array(
					'value' => $id,
					'label' => $name,
				);
			}
			
			return $encoded_statuses;
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
		
		public function filter_shortcode($return_object, $item, $data) {
			
			$code = $data['code'];
			$return_object['renderedHtml'] = do_shortcode($code);
			
			return $return_object;
		}
		
		public function filter_theme_mods($return_object, $item, $data) {
			$return_object = get_theme_mods();
			
			return $return_object;
		}
		
		public function filter_acf_options($return_object, $item, $data) {
			$encoder = new \Wprr\WprrEncoder();
			
			$return_object = $encoder->encode_acf_options();
			
			return $return_object;
		}
		
		public function filter_oembed($return_object, $item, $data) {
			$encoder = new \Wprr\WprrEncoder();
			
			$url = $data['url'];
			$args = null;
			if(isset($data['args'])) {
				$args = array();
				$parameters = explode(",", $data['args']);
				foreach($parameters as $parameter) {
					$temp_array = explode(":", $parameter);
					$args[$temp_array[0]] = $temp_array[1];
				}
			}
			
			$html = wp_oembed_get($url, $args);
			
			if(isset($data['autoplay']) && $data['autoplay'] === "youtube") {
				$html = preg_replace("/(src=\"[^\"]*)(\")/", "$1&autoplay=1$2", $html);
			}
			
			$return_object['renderedHtml'] = $html;
			
			return $return_object;
		}
		
		public function filter_development_generate_data_api_settings($return_object, $item, $data) {
			wprr_generate_data_api_settings();
		}
		
		public function filter_development_wprr_test($return_object, $item, $data) {
			
			
			$editor = wprr_get_data_api()->wordpress()->editor();
			
			$editor->create_object_relation_type('for');
			$editor->create_object_relation_type('in');
			$editor->create_object_relation_types('by', 'based-on');
			
			$editor->create_object_user_relation_type('user-for');
			
		}
		
		public static function test_import() {
			echo("Imported \Wprr\GlobalItemHooks<br />");
		}
	}
?>