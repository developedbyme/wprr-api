<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\PostStatus
	class PostStatus {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("PostStatus::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['postStatus'] = $post->get_data('post_status');
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\PostStatus<br />");
		}
	}
?>