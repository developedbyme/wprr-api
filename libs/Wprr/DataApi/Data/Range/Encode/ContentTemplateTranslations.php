<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\ContentTemplateTranslations
	class ContentTemplateTranslations {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("ContentTemplateTranslations::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$wprr_data_api->range()->encode_object_as($id, 'contentTemplate');
			
			$encoded_data->data['titleTranslations'] = $post->get_fields()->get_field('title')->get_translations();
			$encoded_data->data['contentTranslations'] = $post->get_fields()->get_field('content')->get_translations();
			
			//$encoded_data->data['type'] = //Relation: content-section-type
			
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ContentTemplateTranslations<br />");
		}
	}
?>