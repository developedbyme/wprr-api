<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\MySubscriptions
	class MySubscriptions {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("MySubscriptions::select");
			
			global $wprr_data_api;
			
			$user = $wprr_data_api->user()->get_user_for_call($data);
			
			$query->set_post_type('shop_subscription')->include_all_exisiting_statuses()->meta_query('_customer_user', $user->get_id());
			
		}
		
		public function filter($posts, $data) {
			//var_dump("MySubscriptions::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\MySubscriptions<br />");
		}
	}
?>