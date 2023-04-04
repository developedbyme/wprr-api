<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\ContentTemplate
	class ContentTemplate {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("ContentTemplate::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['name'] = $post->get_meta('name');
			$encoded_data->data['title'] = $post->get_meta('title');
			$encoded_data->data['content'] = $post->get_meta('content');
			
			//$encoded_data->data['type'] = //Relation: content-section-type
			
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ContentTemplate<br />");
		}
	}
?>