<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\MyDraftOrders
	class MyDraftOrders {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("MyDraftOrders::select");
			
			global $wprr_data_api;
			
			$user = $wprr_data_api->user()->get_user_for_call($data);
			
			$query->set_post_type('shop_order')->set_status('draft')->meta_query('_customer_user', $user->get_id());
			
		}
		
		public function filter($posts, $data) {
			//var_dump("MyDraftOrders::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\MyDraftOrders<br />");
		}
	}
?>