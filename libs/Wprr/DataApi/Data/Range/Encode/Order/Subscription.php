<?php
	namespace Wprr\DataApi\Data\Range\Encode\Order;
	
	class Subscription {
		
		function __construct() {
			//echo("\Wprr\DataApi\Data\Range\Encode\Order\Subscription::__construct<br />");
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("Subscription::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$subscription_id = 1*$post->get_meta("_subscription_renewal");
			
			if(!$subscription_id) {
				$query = $wprr_data_api->database()->new_select_query();
				$subscription_id = $query->set_post_type('shop_subscription')->include_all_statuses()->with_parent($id)->get_id();
			}
			
			$encoded_data->data['subscription'] = $wprr_data_api->range()->encode_object_as($subscription_id, 'postStatus');
			$wprr_data_api->range()->encode_object_as($subscription_id, 'subscriptionDates');
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Data\Range\Encode\Order\Subscription<br />");
		}
	}
?>