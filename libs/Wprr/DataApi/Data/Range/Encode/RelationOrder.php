<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\RelationOrder
	class RelationOrder {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("RelationOrder::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['forType'] = $post->get_meta('forType');
			$encoded_data->data['order'] = $post->get_meta('order');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\RelationOrder<br />");
		}
	}
?>