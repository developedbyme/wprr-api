<?php
	namespace Wprr\DataApi\Data\Range\Encode\Order;
	
	class User {
		
		function __construct() {
			//echo("\Wprr\DataApi\Data\Range\Encode\Order\User::__construct<br />");
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("User::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$user_id = (int)$post->get_meta('_customer_user');
			if($user_id) {
				$user = $wprr_data_api->wordpress()->get_user($user_id);
			
				if($user) {
					$encoded_data->data['user'] = $wprr_data_api->range()->encode_user($user);
				}
			}
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Data\Range\Encode\Order\User<br />");
		}
	}
?>