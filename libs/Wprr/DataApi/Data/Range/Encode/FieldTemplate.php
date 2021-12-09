<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\FieldTemplate
	class FieldTemplate {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("FieldTemplate::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['name'] = $post->get_meta('dbmtc_key');
			
			$type_id = (int)$post->get_meta('dbmtc_for_type');
			$term = $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term_by_id($type_id);
			
			$encoded_data->data['forType'] = $wprr_data_api->range()->encode_term($term);
			
			$type = $post->get_single_term_in($wprr_data_api->wordpress()->get_taxonomy('dbm_relation')->get_term('field-type'));
			
			$encoded_data->data['type'] = $wprr_data_api->range()->encode_term($type);
			
			$subtype_encoding_name = 'fieldTemplate/'.$type->get_slug();
			$wprr_data_api->range()->encode_object_if_encoding_exists_as($id, $subtype_encoding_name);
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\FieldTemplate<br />");
		}
	}
?>