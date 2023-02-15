<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\DataImage
	class DataImage {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("DataImage::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data["url"] = $post->get_meta('value')['url'];
			$encoded_data->data["title"] = $post->get_meta('title');
			$encoded_data->data["description"] = $post->get_meta('description');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\DataImage<br />");
		}
	}
?>