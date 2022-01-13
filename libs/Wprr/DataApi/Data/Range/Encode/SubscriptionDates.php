<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\SubscriptionDates
	class SubscriptionDates {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("SubscriptionDates::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['startDate'] = $post->get_meta('_schedule_start');
			
			$end_date = $post->get_meta('_schedule_end');
			if($end_date) {
				$encoded_data->data['endDate'] = $end_date;
			}
			else {
				$encoded_data->data['endDate'] = null;
			}
			
			$next_payment_date = $post->get_meta('_schedule_next_payment');
			if($next_payment_date) {
				$encoded_data->data['nextPaymentDate'] = $next_payment_date;
			}
			else {
				$encoded_data->data['nextPaymentDate'] = null;
			}
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\SubscriptionDates<br />");
		}
	}
?>