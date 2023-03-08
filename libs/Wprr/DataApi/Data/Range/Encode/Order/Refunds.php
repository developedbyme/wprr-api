<?php
	namespace Wprr\DataApi\Data\Range\Encode\Order;
	
	class Refunds {
		
		function __construct() {
			//echo("\Wprr\DataApi\Data\Range\Encode\Order\Refunds::__construct<br />");
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("Refunds::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$query = $wprr_data_api->database()->new_select_query();
			$related_ids = $query->set_post_type('shop_order_refund')->include_all_statuses()->with_parent($id)->get_ids();
			
			$encoded_data->data['refunds'] = $wprr_data_api->range()->encode_objects_as($related_ids, 'order/totals');
			$wprr_data_api->range()->encode_objects_as($related_ids, 'order/items');
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Data\Range\Encode\Order\Refunds<br />");
		}
	}
?>