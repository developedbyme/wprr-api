<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\UserRelation
	class UserRelation {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("UserRelation::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$parent_term = $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term('object-user-relation');
			$type_term = $post->get_single_term_in_with_descendants($parent_term);
			
			$encoded_data->data['type'] = $wprr_data_api->range()->encode_term($type_term);
			
			$encoded_data->data['object'] = $wprr_data_api->range()->encode_object_as((int)$post->get_meta("fromId"), 'objectTypes');
			$encoded_data->data['user'] = $wprr_data_api->range()->encode_user($wprr_data_api->wordpress()->get_user((int)$post->get_meta("toId")));
			
			$encoded_data->data['startAt'] = (int)$post->get_meta("startAt");
			$encoded_data->data['endAt'] = (int)$post->get_meta("endAt");
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\UserRelation<br />");
		}
	}
?>