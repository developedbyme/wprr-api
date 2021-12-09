<?php
	namespace Wprr\DataApi\Data\Range\Encode\Communication;

	// \Wprr\DataApi\Data\Range\Encode\Communication\CommunicationContent
	class CommunicationContent {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("CommunicationContent::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['content'] = $post->get_meta('content');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\CommunicationContent<br />");
		}
	}
?>