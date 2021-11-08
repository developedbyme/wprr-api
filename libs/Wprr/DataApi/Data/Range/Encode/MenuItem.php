<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\MenuItem
	class MenuItem {

		function __construct() {
			
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
			
			if($type === 'post_type') {
				$post_id = (int)$post->get_meta('_menu_item_object_id');
				$encoded_data->data['post'] = $wprr_data_api->range()->encode_object_as($post_id, 'preview');
			}
			else if($type === 'custom') {
				$encoded_data->data['url'] = $post->get_meta('_menu_item_url');
			}
			else if($type === 'taxonomy') {
				$taxonomy = $post->get_meta('_menu_item_object');
				$term_id = (int)$post->get_meta('_menu_item_object_id');
				$term = $wprr_data_api->wordpress()->get_taxonomy($taxonomy)->get_term_by_id($term_id);
				$encoded_data->data['term'] = $wprr_data_api->range()->encode_term($term);
			}
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\MenuItem<br />");
		}
	}
?>