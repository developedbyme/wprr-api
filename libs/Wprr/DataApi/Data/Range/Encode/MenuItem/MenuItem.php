<?php
	namespace Wprr\DataApi\Data\Range\Encode\MenuItem;

	// \Wprr\DataApi\Data\Range\Encode\MenuItem\MenuItem
	class MenuItem {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("MenuItem::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$text = $post->get_post_title();
			$encoded_data->data['text'] = $text ? $text : null;
			$encoded_data->data['order'] = (int)$post->get_data('menu_order');
			$encoded_data->data['parent'] = (int)$post->get_meta('_menu_item_menu_item_parent');
			
			$type = $post->get_meta('_menu_item_type');
			$encoded_data->data['type'] = $type;
			
			$wprr_data_api->range()->encode_object_if_encoding_exists_as($id, 'menuItem/'.$type);
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\MenuItem<br />");
		}
	}
?>