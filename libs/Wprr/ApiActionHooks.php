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
			add_action('wprr/api_action/woocommerce/remove-from-cart', array($this, 'hook_woocommerce_remove_from_cart'), 10, 2);
			add_action('wprr/api_action/woocommerce/apply-dicount-code', array($this, 'hook_woocommerce_apply_discount_code'), 10, 2);
			add_action('wprr/api_action/woocommerce/checkout', array($this, 'hook_woocommerce_checkout'), 10, 2);
			add_action('wprr/api_action/woocommerce/empty-cart', array($this, 'hook_woocommerce_empty_cart'), 10, 2);
			
			add_action('wprr/api_action/woocommerce/set-cart', array($this, 'hook_woocommerce_empty_cart'), 10, 2);
			add_action('wprr/api_action/woocommerce/set-cart', array($this, 'hook_woocommerce_add_to_cart'), 11, 2);
			add_action('wprr/api_action/woocommerce/customer/set-billing-details', array($this, 'hook_woocommerce_customer_set_billing_details'), 10, 2);
			
			add_action('wprr/api_action/woocommerce/subscriptions/start-subscription', array($this, 'hook_woocommerce_subscriptions_start_subscription'), 10, 2);
			
			add_action('wprr/api_action/user/set-email', array($this, 'hook_user_set_email'), 10, 2);
			add_action('wprr/api_action/user/set-password', array($this, 'hook_user_set_password'), 10, 2);
		}

		public function hook_woocommerce_add_to_cart($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_woocommerce_add_to_cart<br />");
			
			$this->ensure_wc_has_cart();
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
		
		public function hook_woocommerce_remove_from_cart($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_woocommerce_add_to_cart<br />");
			
			$this->ensure_wc_has_cart();
			WC()->cart->set_session();
			
			$cart_item_key = $data['key'];
			
			$removed = WC()->cart->remove_cart_item($cart_item_key);
			
			$response_data['removed'] = $removed;
		}
		
		public function hook_woocommerce_apply_discount_code($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_woocommerce_apply_discount_code<br />");
			
			$this->ensure_wc_has_cart();
			WC()->cart->set_session();
			
			$codes = explode(',', $data['code']);
			
			$results = array();
			
			foreach($codes as $code) {
				$result = WC()->cart->add_discount( sanitize_text_field( $code ) );
				$results[] = array('code' => $code, 'result' => $result);
			}
			
			$response_data['results'] = $results;
			
			$notices = array();
			$notice_groups = wc_get_notices();
			foreach($notice_groups as $type => $messages) {
				foreach($messages as $message) {
					$notices[] = array('type' => $type, 'message' => $message);
				}
			}
			$response_data['notices'] = $notices;
			wc_clear_notices();
		}
		
		public function hook_woocommerce_checkout($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_woocommerce_checkout<br />");
			
			$this->ensure_wc_has_cart();
			
			WC()->cart->set_session();
			$cart = WC()->cart;
			
			$order_data = array();
			
			$user_id = get_current_user_id();
			
			if($user_id) {
				$order_data['customer_id'] = $user_id;
				$customer = new \WC_Customer($user_id);
				
				$order_data['billing_first_name'] = $customer->get_billing_first_name();
				$order_data['billing_last_name'] = $customer->get_billing_last_name();
				$order_data['billing_address_1'] = $customer->get_billing_address_1();
				$order_data['billing_address_2'] = $customer->get_billing_address_2();
				$order_data['billing_postcode'] = $customer->get_billing_postcode();
				$order_data['billing_city'] = $customer->get_billing_city();
				$order_data['billing_country'] = $customer->get_billing_country();
				$order_data['billing_phone'] = $customer->get_billing_phone();
			}
			
			
			$order_id = WC()->checkout()->create_order($order_data);
			$order = wc_get_order( $order_id );
			$order->calculate_totals();
			
			if(isset($data['paymentMethod'])) {
				$order->set_payment_method($data['paymentMethod']);
				$order->save();
			}
			
			$response_data['orderId'] = $order_id;
			do_action('wprr/api_action_part/woocommerce/checkout/order_created', $order_id, $order, $response_data);
			
			//Empty cart
			//WC()->cart->empty_cart(); //MEDEBUG: //
		}
		
		//METODO: set payment for order
		
		protected function ensure_wc_has_cart() {
			\Wprr\OddCore\Utils\WoocommerceFunctions::ensure_wc_has_cart();
		}
		
		public function hook_woocommerce_empty_cart($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_woocommerce_empty_cart<br />");
			
			$this->ensure_wc_has_cart();
			
			WC()->cart->set_session();
			WC()->cart->empty_cart();
		}
		
		public function hook_woocommerce_subscriptions_start_subscription($data, &$response_data) {
			
			//METODO: check that the current user is the owner of the subscription
			
			$order_id = $data['orderId'];
			$start_date = $data['startDate'];
			
			$order = wc_get_order($order_id);
			
			$subscription_ids = array();
			
			$subscription_groups = array();
			
			foreach($order->get_items() as $item_id => $item_data) {
				$product = $item_data->get_product();
				$product_id = $product->get_id();
				
				$period = \WC_Subscriptions_Product::get_period( $product_id );
				$interval = \WC_Subscriptions_Product::get_interval( $product_id );
				
				$group_name = $period.'_'.$interval;
				if(!isset($subscription_groups[$group_name])) {
					$subscription_groups[$group_name] = array(
						'period' => $period,
						'interval' => $interval,
						'items' => array()
					);
				}
				
				$subscription_groups[$group_name]['items'][] = $item_data;
			}
			
			foreach($subscription_groups as $group_name => $group_data) {
				$subscription = wcs_create_subscription(array('order_id' => $order_id, 'billing_period' => $group_data['period'], 'billing_interval' => $group_data['interval'], 'start_date' => $start_date));
				
				$subscription->set_payment_method($order->get_payment_method());
				
				foreach($group_data['items'] as $item_data) {
					$product = $item_data->get_product();
					$product_id = $product->get_id();
					
					//METODO: add meta
					
					$subscription->add_product($product, $item_data->get_quantity(), array());
				}
				
				$subscription->calculate_totals();
				$subscription->save();
				$subscription_ids[] = $subscription->get_id();
			}
			
			$response_data['subscriptionIds'] = $subscription_ids;
			
			\WC_Subscriptions_Manager::activate_subscriptions_for_order($order);
			do_action('wprr/api_action_part/woocommerce/subscriptions/created_from_order', $subscription_ids, $order, $response_data);
		}
		
		public function hook_woocommerce_customer_set_billing_details($data, &$response_data) {
			$user_id = $data['userId'];
			
			$current_user_id = get_current_user_id();
			
			if(!current_user_can('edit_others_posts') && $user_id != $current_user_id) {
				$response_data["code"] = 'error';
				$response_data["message"] = 'Not authorized';
				return;
			}
			
			$customer = new \WC_Customer($user_id);
			
			$fields_map = array(
				'firstName' => 'first_name',
				'lastName' => 'last_name',
				'address1' => 'address_1',
				'address2' => 'address_2',
				'postcode' => 'postcode',
				'city' => 'city',
				'country' => 'country',
				'phoneNumber' => 'phone',
			);
			
			$updated_fields = array();
			
			foreach($fields_map as $key => $value) {
				if(isset($data[$key])) {
					$function_name = 'set_billing_'.$value;
					$customer->$function_name($data[$key]);
					$updated_fields[] = $value;
				}
			}
			
			$response_data["updatedFields"] = $updated_fields;
			
			$customer->save();
		}
		
		public function hook_user_set_email($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_user_set_email<br />");
			
			$user_id = (int)$data['userId'];
			
			$current_user_id = get_current_user_id();
			
			if(!current_user_can('edit_others_posts') && $user_id != $current_user_id) {
				throw(new \Exception('Not authorized'));
			}
			
			$email = $data['email'];
			
			$existing_user = get_user_by('email', $email);
			if($existing_user) {
				if($existing_user->ID != $user_id) {
					throw(new \Exception('Email already taken by user'));
				}
				$response_data["id"] = $existing_user->ID;
			}
			else {
				$user_id = wp_update_user(array(
					'ID' => $user_id,
					'user_email' => $email
				));
				
				if ( is_wp_error( $user_id ) ) {
					throw(new \Exception('Could not update user '.$user_id->get_message()));
				}
				$response_data["id"] = $user_id;
			}
		}
		
		public function hook_user_set_password($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_user_set_password<br />");
			
			$user_id = (int)$data['userId'];
			
			$current_user_id = get_current_user_id();
			
			if(!current_user_can('edit_others_posts') && $user_id != $current_user_id) {
				throw(new \Exception('Not authorized'));
			}
			
			$user_id = wp_update_user(array(
				'ID' => $user_id,
				'user_pass' => $data['password']
			));
			
			if ( is_wp_error( $user_id ) ) {
				throw(new \Exception('Could not update user '.$user_id->get_message()));
			}
			$response_data["id"] = $user_id;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\ApiActionHooks<br />");
		}
	}
?>
