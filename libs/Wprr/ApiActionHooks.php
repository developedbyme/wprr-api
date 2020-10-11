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
			add_action('wprr/api_action/woocommerce/add-to-cart-with-multiple-items', array($this, 'hook_woocommerce_add_multiple_items_to_cart'), 10, 2);
			add_action('wprr/api_action/woocommerce/remove-from-cart', array($this, 'hook_woocommerce_remove_from_cart'), 10, 2);
			add_action('wprr/api_action/woocommerce/apply-dicount-code', array($this, 'hook_woocommerce_apply_discount_code'), 10, 2);
			add_action('wprr/api_action/woocommerce/apply-discount-code', array($this, 'hook_woocommerce_apply_discount_code'), 10, 2);
			add_action('wprr/api_action/woocommerce/checkout', array($this, 'hook_woocommerce_checkout'), 10, 2);
			add_action('wprr/api_action/woocommerce/empty-cart', array($this, 'hook_woocommerce_empty_cart'), 10, 2);
			
			add_action('wprr/api_action/woocommerce/set-cart', array($this, 'hook_woocommerce_empty_cart'), 10, 2);
			add_action('wprr/api_action/woocommerce/set-cart', array($this, 'hook_woocommerce_add_to_cart'), 11, 2);
			add_action('wprr/api_action/woocommerce/set-cart-with-multiple-items', array($this, 'hook_woocommerce_empty_cart'), 10, 2);
			add_action('wprr/api_action/woocommerce/set-cart-with-multiple-items', array($this, 'hook_woocommerce_add_multiple_items_to_cart'), 11, 2);
			add_action('wprr/api_action/woocommerce/customer/set-billing-details', array($this, 'hook_woocommerce_customer_set_billing_details'), 10, 2);
			
			add_action('wprr/api_action/woocommerce/subscriptions/start-subscription', array($this, 'hook_woocommerce_subscriptions_start_subscription'), 10, 2);
			add_action('wprr/api_action/woocommerce/add-product-meta-links', array($this, 'hook_woocommerce_add_product_meta_links'), 10, 2);
			add_action('wprr/api_action/woocommerce/add-product-review', array($this, 'hook_woocommerce_add_product_review'), 10, 2);
			
			add_action('wprr/api_action/user/test-nonce', array($this, 'hook_user_test_nonce'), 10, 2);
			add_action('wprr/api_action/user/set-email', array($this, 'hook_user_set_email'), 10, 2);
			add_action('wprr/api_action/user/set-password', array($this, 'hook_user_set_password'), 10, 2);
			
			add_action('wprr/api_action/wprr/save-initial-load-cache', array($this, 'hook_wprr_save_initial_load_cache'), 10, 2);
		}
		
		public function add_item_to_cart($data) {
			//echo("add_item_to_cart");
			//var_dump($data);
			
			$product_id = $data['id'];
			$quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
			$variation_id = 0;
			$variation_data = array();
			$item_data = array();
			if(isset($data['itemData'])) {
				$item_data = $data['itemData'];
			}
			
			$add_to_cart = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation_data, $item_data);
			
			return $add_to_cart;
		}

		public function hook_woocommerce_add_to_cart($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_woocommerce_add_to_cart<br />");
			
			$this->ensure_wc_has_cart();
			WC()->cart->set_session();
			
			$response_data['addedToCart'] = $this->add_item_to_cart($data);
		}
		
		public function hook_woocommerce_add_multiple_items_to_cart($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_woocommerce_add_multiple_items_to_cart<br />");
			
			$this->ensure_wc_has_cart();
			WC()->cart->set_session();
			
			$return_data = array();
			
			foreach($data['items'] as $item) {
				$return_data[] = $this->add_item_to_cart($item);
			}
			
			if(isset($data['sessionVariables'])) {
				foreach($data['sessionVariables'] as $key => $value) {
					WC()->session->set($key, $value);
				}
			}
			
			
			$response_data['addedToCart'] = $return_data;
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
			
			$codes = explode(',', $data['code']);
			
			$results = array();
			
			foreach($codes as $code) {
				$result = WC()->cart->apply_coupon($code);
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
			
			WC()->cart->set_session();
			
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
				$order_data['billing_company'] = $customer->get_billing_company();
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
			wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );
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
				
				if(is_wp_error($subscription)) {
					$error = $subscription;
					throw(new \Exception($error->get_error_message()));
				}
				
				$subscription->set_payment_method($order->get_payment_method());
				
				foreach($group_data['items'] as $item_data) {
					$product = $item_data->get_product();
					$product_id = $product->get_id();
					
					//METODO: add meta
					
					$fields_map = array('first_name', 'last_name', 'company', 'address_1', 'address_2', 'postcode', 'city', 'country', 'phone');
					
					foreach($fields_map as  $value) {
						$set_function_name = 'set_billing_'.$value;
						$get_function_name = 'get_billing_'.$value;
						$subscription->$set_function_name($order->$get_function_name());
					}
					
					//METODO: copy over shipping
					
					$subscription->add_product($product, $item_data->get_quantity(), array());
				}
				
				//METODO: copy over discount codes
				
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
		
		public function hook_woocommerce_add_product_review($data, &$response_data) {
			
			$user_id = (int)$data['userId'];
			
			$current_user_id = get_current_user_id();
			
			if(!current_user_can('edit_others_posts') && $user_id != $current_user_id) {
				throw(new \Exception('Not authorized'));
			}
			
			$product_id = $data['productId'];
			//METODO: check that it is a product
			$review = isset($data['review']) ? sanitize_text_field($data['review']) : '';
			
			
			$user_data = get_userdata($user_id);
			
			$comment_id = wp_insert_comment(array(
				'comment_post_ID'      => $product_id,
				'comment_author'       => $user_data->display_name,
				'comment_author_email' => $user_data->email,
				'comment_author_url'   => '',
				'comment_content'      => $review,
				'comment_type'         => '',
				'comment_parent'       => 0,
				'user_id'              => $user_id,
				'comment_author_IP'    => $_SERVER['HTTP_CLIENT_IP'],
				'comment_agent'        => $_SERVER['HTTP_USER_AGENT'],
				'comment_date'         => date('Y-m-d H:i:s'),
				'comment_approved'     => 1,
			));
			
			update_comment_meta($comment_id, 'rating', $data['rating']);
			
			$response_data['id'] = $comment_id;
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
		
		public function hook_user_test_nonce($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_user_test_nonce<br />");
			
			$current_user_id = get_current_user_id();
			
			if(!$current_user_id) {
				throw(new \Exception('No user'));
			}
			
			$response_data["id"] = $current_user_id;
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
		
		protected function _update_product_link($post_id) {
			$order = wc_get_order($post_id);
			$meta_name = 'wprr_product_id';
			delete_post_meta($post_id, $meta_name);
			foreach($order->get_items() as $item_id => $item_data) {
				$current_id = $item_data->get_product_id();
				add_post_meta($post_id, $meta_name, $current_id);
			}
		}
		
		public function hook_woocommerce_add_product_meta_links($data, &$response_data) {
			$order_ids = dbm_new_query('shop_subscription')->set_field('post_status', array_keys( wc_get_order_statuses() ))->get_post_ids();
			foreach($order_ids as $post_id) {
				$this->_update_product_link($post_id);
			}
			
			$subscription_ids = dbm_new_query('shop_subscription')->set_field('post_status', array( 'wc-pending', 'wc-active', 'wc-on-hold', 'wc-pending-cancel', 'wc-cancelled', 'wc-expired' ))->get_post_ids();
			foreach($subscription_ids as $post_id) {
				$this->_update_product_link($post_id);
			}
		}
		
		public function hook_wprr_save_initial_load_cache($data, &$response_data) {
			$upload_dir = wp_upload_dir(null, false);
			
			$paths = $data['paths'];
			$permalink = $data['permalink'];
			
			$valid_paths = array();
			foreach($paths as $path) {
				$valid = apply_filters('wprr/initial-load-cache/can-store-path', true, $path);
				if($valid) {
					$valid_paths[] = $path;
				}
			}
			
			$salt = apply_filters('wprr/initial-load-cache/salt', 'wvIUIAULTxKicDpbkzyPpVi5wskSe6Yxy0Uq4wCqbAui1wVKAKmsVhN7JOhGbFQohVs9pnpQoS1dWGkL');
			
			$upload_path = $upload_dir['basedir'].'/wprr-initial-load-cache/'.md5($permalink.$salt).'.json';
			
			$parent_directory = dirname($upload_path);
		
			if (!file_exists($parent_directory)) {
				mkdir($parent_directory, 0755, true);
			}
			
			$file = fopen($upload_path, 'w');
			fwrite($file, json_encode($valid_paths));
			fclose($file);
		}
		
		public static function test_import() {
			echo("Imported \Wprr\ApiActionHooks<br />");
		}
	}
?>
