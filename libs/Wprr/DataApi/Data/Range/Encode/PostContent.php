<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\PostContent
	class PostContent {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("PostContent::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['content'] = $post->get_post_content();
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\PostContent<br />");
		}
	}
?>