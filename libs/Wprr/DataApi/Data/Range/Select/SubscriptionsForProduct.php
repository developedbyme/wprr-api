<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\SubscriptionsForProduct
	class SubscriptionsForProduct {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("SubscriptionsForProduct::select");
			
			global $wprr_data_api;
			
			if(!isset($data['product'])) {
				throw(new \Exception('No product set'));
			}
			
			$product = (int)$data['product'];
			
			$user = $wprr_data_api->user()->get_user_for_call($data);
			$is_ok = $user->is_trusted();
			if(!$is_ok) {
				throw(new \Exception('User '.$user->get_id().' is not allowed to get subscriptions'));
			}
			
			$meta_query = 'SELECT order_item_id as id FROM wp_woocommerce_order_itemmeta WHERE meta_key = "_product_id" AND meta_value = '.$product;
			$meta_rows = $wprr_data_api->database()->query($meta_query);
			
			$order_item_ids = array_map(function($row) {return $row['id'];}, $meta_rows);
			
			$order_query = 'SELECT DISTINCT(order_id) as id FROM wp_woocommerce_order_items WHERE order_item_id IN ('.implode(',', $order_item_ids).')';
			$order_rows = $wprr_data_api->database()->query($order_query);
			
			$order_ids = array_map(function($row) {return $row['id'];}, $order_rows);
			
			$query->set_post_type('shop_subscription')->include_all_exisiting_statuses()->include_only($order_ids);
			
		}
		
		public function filter($posts, $data) {
			//var_dump("SubscriptionsForProduct::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\SubscriptionsForProduct<br />");
		}
	}
?>