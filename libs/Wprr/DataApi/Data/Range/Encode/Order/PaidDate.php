<?php
	namespace Wprr\DataApi\Data\Range\Encode\Order;
	
	class PaidDate {
		
		function __construct() {
			//echo("\Wprr\DataApi\Data\Range\Encode\Order\PaidDate::__construct<br />");
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("PaidDate::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['paidDate'] = $post->get_meta('_paid_date');
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Data\Range\Encode\Order\PaidDate<br />");
		}
	}
?>