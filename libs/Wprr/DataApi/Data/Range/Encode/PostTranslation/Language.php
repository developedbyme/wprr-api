<?php
	namespace Wprr\DataApi\Data\Range\Encode\PostTranslation;

	// \Wprr\DataApi\Data\Range\Encode\PostTranslation\Language
	class Language {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Language::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['language'] = $post->get_meta('language');
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Language<br />");
		}
	}
?>