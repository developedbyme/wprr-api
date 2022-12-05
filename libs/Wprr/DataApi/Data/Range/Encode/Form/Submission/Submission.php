<?php
	namespace Wprr\DataApi\Data\Range\Encode\Form\Submission;

	// \Wprr\DataApi\Data\Range\Encode\Submission
	class Submission {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Submission::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$wprr_data_api->range()->encode_object_as($id, 'value');
			
			$related_items = $post->object_relation_query('in:uploaded-to:uploaded-file');
			$related_ids = array_map(function($item) {return (int)$item->get_id();}, $related_items);
			
			$encoded_data->data['files'] = $wprr_data_api->range()->encode_objects_as($related_ids, 'uploadedFile');
			$wprr_data_api->range()->encode_objects_as($related_ids, 'identifier');
			
			$user = $post->single_object_relation_query('user:by');
			
			if($user) {
				$encoded_data->data['by'] = $wprr_data_api->range()->encode_user($user);
			}
			else {
				$encoded_data->data['by'] = null;
			}
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Submission<br />");
		}
	}
?>