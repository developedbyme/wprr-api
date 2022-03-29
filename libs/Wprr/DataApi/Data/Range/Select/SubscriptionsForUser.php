<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\SubscriptionsForUser
	class SubscriptionsForUser {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("SubscriptionsForUser::select");
			
			global $wprr_data_api;
			
			if(!isset($data['userId'])) {
				throw(new \Exception('No userId set'));
			}
			
			$user_id = (int)$data['userId'];
			
			$user = $wprr_data_api->user()->get_user_for_call($data);
			$is_ok = $user->is_trusted();
			if(!$is_ok) {
				throw(new \Exception('User '.$user->get_id().' is not allowed to get subscriptions'));
			}
			
			$query->set_post_type('shop_subscription')->include_all_exisiting_statuses()->meta_query('_customer_user', $user_id);
			
		}
		
		public function filter($posts, $data) {
			//var_dump("SubscriptionsForUser::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\SubscriptionsForUser<br />");
		}
	}
?>