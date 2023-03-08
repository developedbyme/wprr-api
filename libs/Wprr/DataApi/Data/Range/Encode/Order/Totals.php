<?php
	namespace Wprr\DataApi\Data\Range\Encode\Order;
	
	class Totals {
		
		function __construct() {
			//echo("\Wprr\DataApi\Data\Range\Encode\Order\Totals::__construct<br />");
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("Totals::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$post_type = $post->get_data('post_type');
			
			if($post_type === 'shop_order_refund') {
				$encoded_data->data['total'] = -1*(float)$post->get_meta('_refund_amount');
			}
			else {
				$encoded_data->data['total'] = (float)$post->get_meta('_order_total');
			}
			
			$encoded_data->data['tax'] = (float)$post->get_meta('_order_tax');
			$encoded_data->data['subtotal'] = $encoded_data->data['total']-$encoded_data->data['tax'];
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Data\Range\Encode\Order\Totals<br />");
		}
	}
?>