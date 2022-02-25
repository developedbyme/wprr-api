<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\OrdersForSubscriptions
	class OrdersForSubscriptions {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("OrdersForSubscriptions::select");
			
			global $wprr_data_api;
			
			if(!isset($data['subscriptions'])) {
				throw(new \Exception('No subscriptions set'));
			}
			
			$user = $wprr_data_api->user()->get_user_for_call($data);
			$is_ok = $user->is_trusted();
			if(!$is_ok) {
				throw(new \Exception('User '.$user->get_id().' is not allowed to get orders'));
			}
			
			$subscriptions = $wprr_data_api->wordpress()->get_posts(array_map(function($id) {return (int)$id;}, explode(',', $data['subscriptions'])));
			
			$order_ids = array();
			
			foreach($subscriptions as $subscription) {
				$parent = $subscription->get_parent();
			
				$subquery = $wprr_data_api->database()->new_select_query();
				
				$children_ids = $subquery->set_post_type('shop_order')->include_all_statuses()->meta_query('_subscription_renewal', $subscription->get_id())->get_ids();
				
				$order_ids = array_merge($order_ids, $children_ids);
				if($parent) {
					$order_ids[] = $parent->get_id();
				}
			}
			
			$query->set_post_type('shop_order')->include_all_exisiting_statuses()->include_only($order_ids);
			
		}
		
		public function filter($posts, $data) {
			//var_dump("OrdersForSubscriptions::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\OrdersForSubscriptions<br />");
		}
	}
?>