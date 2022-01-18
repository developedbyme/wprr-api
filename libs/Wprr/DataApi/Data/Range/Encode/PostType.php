<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\PostType
	class PostType {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("PostType::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['postType'] = $post->get_data('post_type');
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\PostType<br />");
		}
	}
?>