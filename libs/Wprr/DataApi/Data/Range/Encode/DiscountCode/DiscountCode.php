<?php
	namespace Wprr\DataApi\Data\Range\Encode\DiscountCode;

	// \Wprr\DataApi\Data\Range\Encode\DiscountCode\DiscountCode
	class DiscountCode {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("DiscountCode::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['code'] = $post->get_post_title();
			$encoded_data->data['amount'] = 1*$post->get_meta('coupon_amount');
			
			$type = $post->get_meta('discount_type');
			$encoded_data->data['type'] = $type;
			
			if($type) {
				$subtype_encoding_name = 'discountCode/'.$type;
				$wprr_data_api->range()->encode_object_if_encoding_exists_as($id, $subtype_encoding_name);
			}
			
			//_wcs_number_payments
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\DiscountCode<br />");
		}
	}
?>