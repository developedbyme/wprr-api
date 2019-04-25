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
			
			add_action('wprr/api_action/woocommerce/set-cart', array($this, 'hook_woocommerce_empty_cart'), 10, 2);
			add_action('wprr/api_action/woocommerce/set-cart', array($this, 'hook_woocommerce_add_to_cart'), 11, 2);
			
			add_action('wprr/api_action/woocommerce/subscriptions/start-subscription', array($this, 'hook_woocommerce_subscriptions_start_subscription'), 10, 2);

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
			
			$cart = WC()->cart;
			$order_id = WC()->checkout()->create_order(array('customer_id' => get_current_user_id()));
			$order = wc_get_order( $order_id );
			//update_post_meta($order_id, '_customer_user', get_current_user_id());
			$order->calculate_totals();
			
			$order->set_payment_method('manual'); //MEDEBUG
			
			$response_data['orderId'] = $order_id;
			
			//Empty cart
			WC()->cart->empty_cart();
		}
		
		//METODO: set payment for order
		
		public function hook_woocommerce_empty_cart($data, &$response_data) {
			//echo("\Wprr\ApiActionHooks::hook_woocommerce_empty_cart<br />");
			
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
		
		public static function test_import() {
			echo("Imported \Wprr\ApiActionHooks<br />");
		}
	}
?>
