<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Action
	class Action {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Action::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['data'] = $post->get_meta('value');
			
			$relations = $post->get_incoming_direction()->get_type('for')->get_relations('type/action-status');
			$relation_ids = array();
			foreach($relations as $relation) {
				$relation_ids[] = $relation->get_id();
			}
			
			$encoded_data->data['statusRelations'] = $wprr_data_api->range()->encode_objects_as($relation_ids, 'relation');
			
			$ids = $post->get_outgoing_direction()->get_type('from')->get_object_ids('*');
			
			$encoded_data->data['from'] = $wprr_data_api->range()->encode_objects_as($ids, 'postTitle');
			$wprr_data_api->range()->encode_objects_as($ids, 'objectTypes');
			
			$type = $post->get_incoming_direction()->get_type('for')->get_single_object_id('type/action-type');
			$encoded_data->data['type'] = $wprr_data_api->range()->encode_object_as($type, 'type');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Action<br />");
		}
	}
?>