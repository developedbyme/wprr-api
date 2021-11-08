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
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\PostTitle<br />");
		}
	}
?>