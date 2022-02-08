<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\TemplatePosition
	class TemplatePosition {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("TemplatePosition::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['name'] = $post->get_meta('name');
			$encoded_data->data['identifier'] = $post->get_meta('identifier');
			$encoded_data->data['description'] = $post->get_meta('description');
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\TemplatePosition<br />");
		}
	}
?>