<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\PostTitle
	class PostTitle {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("PostTitle::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			$encoded_data->data['title'] = $post->get_post_title();
			
			$encoded_data->data['meta'] = $post->get_meta('lagerkungen_facility_name');
			
			//var_dump($encoded_data);
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\PostTitle<br />");
		}
	}
?>