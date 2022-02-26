<?php
	namespace Wprr\DataApi\Data\Range\Encode\Subscription;
	
	class Orders {
		
		function __construct() {
			//echo("\Wprr\DataApi\Data\Range\Encode\Subscription\Orders::__construct<br />");
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("Orders::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$parent = $post->get_parent();
			
			$query = $wprr_data_api->database()->new_select_query();
			
			$children_ids = $query->set_post_type('shop_order')->include_all_exisiting_statuses()->meta_query('_subscription_renewal', $id)->get_ids();
			
			$children_ids = array_reverse($children_ids);
			
			if($parent) {
				$children_ids[] = $parent->get_id();
			}
			
			$encoded_data->data['orders'] = $wprr_data_api->range()->encode_objects_as($children_ids, 'order/totals');
			
			$wprr_data_api->range()->encode_objects_as($children_ids, 'publishDate');
			$wprr_data_api->range()->encode_objects_as($children_ids, 'postStatus');
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Data\Range\Encode\Subscription\Orders<br />");
		}
	}
?>