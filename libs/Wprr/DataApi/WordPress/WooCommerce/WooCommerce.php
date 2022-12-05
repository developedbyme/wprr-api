<?php
	namespace Wprr\DataApi\WordPress\WooCommerce;

	// \Wprr\DataApi\WordPress\WordPress\WooCommerce
	class WooCommerce {

		function __construct() {
			
		}
		
		public function get_order_items($post) {
			global $wprr_data_api;
			
			$id = $post->get_id();
			
			$query = 'SELECT order_item_id as id, order_item_name as name, order_item_type as type FROM '.DB_TABLE_PREFIX.'woocommerce_order_items WHERE order_id = '.((int)$id);
			$rows = $wprr_data_api->database()->query($query);
			
			$ids = array();
			$items = array();
				
			foreach($rows as $row) {
				$current_line_item = array(
					'type' => $row['type'],
					'name' => $row['name'],
					'meta' => array()
				);
				
				$items[$row['id']] = $current_line_item;
				$ids[] = $row['id'];
			}
			
			if(!empty($ids)) {
				$query = 'SELECT order_item_id as id, meta_key, meta_value FROM '.DB_TABLE_PREFIX.'woocommerce_order_itemmeta WHERE order_item_id IN ('.implode(',', $ids).')';
				$rows = $wprr_data_api->database()->query($query);
			
				foreach($rows as $row) {
					$id = $row['id'];
				
					$key = $row['meta_key'];
					if(!isset($items[$id]['meta'][$key])) {
						$items[$id]['meta'][$key] = array();
					}
					$items[$id]['meta'][$key][] = $row['meta_value'];
				}
			}
			
			$line_items = array();
			$coupons = array();
			
			foreach($items as $id => $item) {
				if($item['type'] === 'line_item') {
					$current_item = array();
					
					$current_item['id'] = $id;
					$current_item['quantity'] = (float)$item['meta']['_qty'][0];
					$current_item['product'] = $wprr_data_api->wordpress()->get_post((int)$item['meta']['_product_id'][0]);
					$current_item['total'] = (float)$item['meta']['_line_total'][0];
					$current_item['tax'] = (float)$item['meta']['_line_tax'][0];
					
					$line_items[] = $current_item;
				}
				if($item['type'] === 'coupon') {
					$current_coupon = array();
					
					$current_coupon['id'] = $id;
					$current_coupon['code'] = $item['name'];
					$current_coupon['total'] = (float)$item['meta']['discount_amount'][0];
					$current_coupon['tax'] = (float)$item['meta']['discount_amount_tax'][0];
					
					$coupons[] = $current_coupon;
				}
			}
			
			$return_data = array('items' => $line_items, 'coupons' => $coupons);
			
			return $return_data;
		}
		
		public function get_order_products($post) {
			
			$return_array = array();
			
			$items = $this->get_order_items($post)['items'];
			foreach($items as $item) {
				$return_array[] = $item['product'];
			}
			
			return $return_array;
		}
		
		public function get_subscription_from_order($post) {
			global $wprr_data_api;
			
			$subscription_id = 1*$post->get_meta("_subscription_renewal");
			
			if(!$subscription_id) {
				$query = $wprr_data_api->database()->new_select_query();
				$subscription_id = $query->set_post_type('shop_subscription')->include_all_statuses()->with_parent($post->get_id())->get_id();
			}
			
			if(!$subscription_id) {
				return null;
			}
			
			return $wprr_data_api->wordpress()->get_post($subscription_id);
		}
		
		public function get_subscription_orders($subscription) {
			global $wprr_data_api;
			
			$parent = $subscription->get_parent();
			$order_ids = $wprr_data_api->database()->new_select_query()->set_post_type('shop_order')->include_all_exisiting_statuses()->meta_query('_subscription_renewal', $subscription->get_id())->get_ids();
		
			if($parent) {
				$order_ids[] = $parent->get_id();
			}
			
			return $wprr_data_api->wordpress()->get_posts($order_ids);
		}
		
		public function has_coupon_by_name($order, $code) {
			$coupons = $this->get_order_items($order)['coupons'];
			return in_array($code, array_column($coupons, 'code'));
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\WordPress\WooCommerce<br />");
		}
	}
?>