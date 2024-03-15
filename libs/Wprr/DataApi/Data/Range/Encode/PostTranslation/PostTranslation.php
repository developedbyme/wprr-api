<?php
	namespace Wprr\DataApi\Data\Range\Encode\PostTranslation;

	// \Wprr\DataApi\Data\Range\Encode\PostTranslation\PostTranslation
	class PostTranslation {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("PostTranslation::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$wprr_data_api->range()->encode_object_as($id, 'postTranslation/language');
			$wprr_data_api->range()->encode_object_as($id, 'postTranslation/translations');
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\PostTranslation<br />");
		}
	}
?>