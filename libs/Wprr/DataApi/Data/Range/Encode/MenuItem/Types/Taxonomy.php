<?php
	namespace Wprr\DataApi\Data\Range\Encode\MenuItem\Types;

	// \Wprr\DataApi\Data\Range\Encode\MenuItem\Types\Taxonomy
	class Taxonomy {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("Taxonomy::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$taxonomy = $post->get_meta('_menu_item_object');
			$term_id = (int)$post->get_meta('_menu_item_object_id');
			$term = $wprr_data_api->wordpress()->get_taxonomy($taxonomy)->get_term_by_id($term_id);
			$encoded_data->data['term'] = $wprr_data_api->range()->encode_term($term);
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Taxonomy<br />");
		}
	}
?>