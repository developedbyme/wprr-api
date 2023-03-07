<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\FormSubmission
	class FormSubmission {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("FormSubmission::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$wprr_data_api->range()->encode_object_as($id, 'value');
			$wprr_data_api->range()->encode_object_as($id, 'publishDate');
			
			$encoded_data->data['formName'] = $wprr_data_api->range()->encode_object_as($post->get_incoming_direction()->get_type('for')->get_single_object_id('type/form-name'), 'type');
			$encoded_data->data['files'] = $wprr_data_api->range()->encode_objects_as($post->get_incoming_direction()->get_type('uploaded-to')->get_object_ids('uploaded-file'), 'uploadedFile');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\FormSubmission<br />");
		}
	}
?>