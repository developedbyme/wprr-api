<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\InternalMessage
	class InternalMessage {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("InternalMessage::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			
			$encoded_data->data['body'] = $post->get_post_content();
			$encoded_data->data['date'] = $post->get_data('post_date');
			
			$group = $post->get_outcoming_direction()->get_type('message-in')->get_single_object_id('*');
			
			$encoded_data->data['group'] = $wprr_data_api->range()->encode_object_as($group, 'id');
			
			$parent_term = $wprr_data_api->wordpress()->get_taxonomy('dbm_relation')->get_term('internal-message-types');
			$type_term = $post->get_single_term_in($parent_term);
			
			$encoded_data->data['type'] = $wprr_data_api->range()->encode_term($type_term);
			
			$type_encoding = 'internalMessage/'.$type_term->get_slug();
			$wprr_data_api->range()->encode_object_if_encoding_exists_as($id, $type_encoding);
			
			$user_id = (int)$post->get_data('post_author');
			if($user_id) {
				$user = $wprr_data_api->wordpress()->get_user($user_id);
			
				$encoded_data->data['user'] = $wprr_data_api->range()->encode_user($user);
			}
			else {
				$encoded_data->data['user'] = null;
			}
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\InternalMessage<br />");
		}
	}
?>