<?php
	namespace Wprr\DataApi\Data\Range\Encode\SignupInvite;

	// \Wprr\DataApi\Data\Range\Encode\SignupInvite
	class SignupInvite {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("SignupInvite::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['data'] = $post->get_meta('data');
			
			$related_post = $post->single_object_relation_query('out:invite-for:*');
			$encoded_data->data['for'] = $related_post ? $wprr_data_api->range()->encode_object_as($related_post->get_id(), 'id') : 0;
			
			//METODO
			$related_post = 0; //$post->single_object_relation_query('in:for:type/invite-status');
			$encoded_data->data['status'] = $related_post ? $wprr_data_api->range()->encode_object_as($related_post->get_id(), 'id') : 0;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\SignupInvite<br />");
		}
	}
?>