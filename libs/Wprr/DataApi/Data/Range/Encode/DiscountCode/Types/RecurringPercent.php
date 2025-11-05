<?php
	namespace Wprr\DataApi\Data\Range\Encode\DiscountCode\Types;

	// \Wprr\DataApi\Data\Range\Encode\DiscountCode\\Types\RecurringPercent
	class RecurringPercent {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("RecurringPercent::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['numberOfPayments'] = (int)$post->get_meta('_wcs_number_payments');
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\RecurringPercent<br />");
		}
	}
?>