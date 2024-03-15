<?php
	namespace Wprr\DataApi\Data\Range\Encode\Representation;

	// \Wprr\DataApi\Data\Range\Encode\Representation\Representation
	class Representation {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Representation::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$wprr_data_api->range()->encode_object_as($post->get_id(), 'value');
			$wprr_data_api->range()->encode_object_as($post->get_id(), 'value/translations');
			
			$related_post = $post->single_object_relation_query('out:by:*');
			$encoded_data->data['by'] = $related_post ? $wprr_data_api->range()->encode_object_as($related_post->get_id(), 'postTranslation/translations') : 0;
			if($related_post) {
				$wprr_data_api->range()->encode_object_as($related_post->get_id(), 'permalink');
				$wprr_data_api->range()->encode_object_as($related_post->get_id(), 'postTitle');
			}
			
			$related_post = $post->single_object_relation_query('out:of:*');
			$encoded_data->data['of'] = $related_post ? $wprr_data_api->range()->encode_object_as($related_post->get_id(), 'id') : 0;
			
			$related_post = $post->single_object_relation_query('in:for:type/representation-type');
			$encoded_data->data['type'] = $related_post ? $wprr_data_api->range()->encode_object_as($related_post->get_id(), 'type') : 0;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Representation<br />");
		}
	}
?>