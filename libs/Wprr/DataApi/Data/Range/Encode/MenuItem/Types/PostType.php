<?php
	namespace Wprr\DataApi\Data\Range\Encode\MenuItem\Types;

	// \Wprr\DataApi\Data\Range\Encode\MenuItem\Types\PostType
	class PostType {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("PostType::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$post_id = (int)$post->get_meta('_menu_item_object_id');
			$encoded_data->data['post'] = $wprr_data_api->range()->encode_object_as($post_id, 'preview');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\PostType<br />");
		}
	}
?>