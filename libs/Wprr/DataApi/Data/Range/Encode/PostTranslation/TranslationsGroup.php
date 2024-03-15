<?php
	namespace Wprr\DataApi\Data\Range\Encode\PostTranslation;

	// \Wprr\DataApi\Data\Range\Encode\PostTranslation\TranslationsGroup
	class TranslationsGroup {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("TranslationsGroup::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$related_items = $post->object_relation_query('in:in:*');
			$related_ids = array_map(function($item) {return (int)$item->get_id();}, $related_items);
			
			$encoded_data->data['posts'] = $wprr_data_api->range()->encode_objects_as($related_ids, 'postTranslation/language');
			$wprr_data_api->range()->encode_objects_as($related_ids, 'postTranslation/translations');
			
			$related_post = $post->single_object_relation_query('out:of:*');
			$encoded_data->data['of'] = $related_post ? $wprr_data_api->range()->encode_object_as($related_post->get_id(), 'postTranslation/language') : 0;
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\TranslationsGroup<br />");
		}
	}
?>