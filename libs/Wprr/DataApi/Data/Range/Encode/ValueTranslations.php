<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\ValueTranslations
	class ValueTranslations {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("ValueTranslations::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$wprr_data_api->range()->encode_object_as($id, 'value');
			$encoded_data->data['valueTranslations'] = $post->get_fields()->get_field('value')->get_translations();
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ValueTranslations<br />");
		}
	}
?>