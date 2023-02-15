<?php
	namespace Wprr\DataApi\Data\Range\Encode\Group;

	// \Wprr\DataApi\Data\Range\Encode\Group\OrderedGroup
	class OrderedGroup {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("OrderedGroup::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$parts = $post->get_incoming_direction()->get_type('in')->get_object_ids_in_order('*', 'order');
			$encoded_data->data['items'] = $wprr_data_api->range()->encode_objects_as($parts, 'id');
			
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\OrderedGroup<br />");
		}
	}
?>