<?php
	namespace Wprr\DataApi\Data\Range\Encode\PostTranslation;

	// \Wprr\DataApi\Data\Range\Encode\PostTranslation\Translations
	class Translations {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Translations::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$related_post = $post->single_object_relation_query('out:in:group/translations-group');
			$encoded_data->data['translations'] = $related_post ? $wprr_data_api->range()->encode_object_as($related_post->get_id(), 'postTranslation/translationsGroup') : 0;
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Translations<br />");
		}
	}
?>