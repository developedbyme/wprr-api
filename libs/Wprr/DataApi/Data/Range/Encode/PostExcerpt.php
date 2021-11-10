<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\PostExcerpt
	class PostExcerpt {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("PostExcerpt::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['excerpt'] = $post->get_data('post_excerpt');
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\PostExcerpt<br />");
		}
	}
?>