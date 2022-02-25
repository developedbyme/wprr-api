<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\OrdersForDiscountCode
	class OrdersForDiscountCode {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("OrdersForDiscountCode::select");
			
			global $wprr_data_api;
			
			if(!isset($data['discountCode'])) {
				throw(new \Exception('No discountCode set'));
			}
			
			$discountCode = $data['discountCode'];
			
			$user = $wprr_data_api->user()->get_user_for_call($data);
			$is_ok = $user->is_trusted();
			if(!$is_ok) {
				throw(new \Exception('User '.$user->get_id().' is not allowed to get orders'));
			}
			
			$order_query = 'SELECT DISTINCT(order_id) as id FROM wp_woocommerce_order_items WHERE order_item_type = "coupon" AND order_item_name = "'.$wprr_data_api->database()->escape($discountCode).'"';
			$order_rows = $wprr_data_api->database()->query($order_query);
			
			$order_ids = array_map(function($row) {return $row['id'];}, $order_rows);
			
			$query->set_post_type('shop_order')->include_all_exisiting_statuses()->include_only($order_ids);
			
		}
		
		public function filter($posts, $data) {
			//var_dump("OrdersForDiscountCode::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\OrdersForDiscountCode<br />");
		}
	}
?>