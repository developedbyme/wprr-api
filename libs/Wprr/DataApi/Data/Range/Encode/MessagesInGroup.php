<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\MessagesInGroup
	class MessagesInGroup {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("MessagesInGroup::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$messages = $post->get_incoming_direction()->get_type('message-in')->get_object_ids('internal-message');
			
			$encoded_data->data['messages'] = $wprr_data_api->range()->encode_objects_as($messages, 'internalMessage');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\MessagesInGroup<br />");
		}
	}
?>