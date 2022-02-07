<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\ProcessPart
	class ProcessPart {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("ProcessPart::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['name'] = $post->get_meta('name');
			$encoded_data->data['description'] = $post->get_meta('description');
			$encoded_data->data['identifier'] = $post->get_meta('identifier');
			$encoded_data->data['type'] = $post->get_meta('type');
			$encoded_data->data['value'] = $post->get_meta('value');
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ProcessPart<br />");
		}
	}
?>