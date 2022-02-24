<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\Subscriptions
	class Subscriptions {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("Subscriptions::select");
			
			global $wprr_data_api;
			
			$user = $wprr_data_api->user()->get_user_for_call($data);
			$is_ok = $user->is_trusted();
			if(!$is_ok) {
				throw(new \Exception('User '.$user->get_id().' is not allowed to get orders'));
			}
			
			$query->set_post_type('shop_subscription')->include_all_statuses();
			
		}
		
		public function filter($posts, $data) {
			//var_dump("Subscriptions::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Subscriptions<br />");
		}
	}
?>