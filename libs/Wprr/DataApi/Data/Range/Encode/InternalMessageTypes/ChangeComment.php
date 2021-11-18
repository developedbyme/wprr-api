<?php
	namespace Wprr\DataApi\Data\Range\Encode\InternalMessageTypes;

	// \Wprr\DataApi\Data\Range\Encode\InternalMessageTypes\ChangeComment
	class ChangeComment {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("ChangeComment::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['test'] = "test";
			
			$encoded_data->data['oldValue'] = $post->get_meta('oldValue');
			$encoded_data->data['newValue'] = $post->get_meta('newValue');
			$encoded_data->data['changeType'] = $post->get_meta('changeType');
			$encoded_data->data['field'] = $post->get_meta('field');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ChangeComment<br />");
		}
	}
?>