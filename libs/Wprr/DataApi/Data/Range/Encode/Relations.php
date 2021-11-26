<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Relations
	class Relations {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Relations::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_relations = array();
			
			$current_direction = $post->get_incoming_direction();
			$types = $current_direction->get_types();
			foreach($types as $type) {
				$relations = $type->get_relations('*', false);
				$relation_ids = array();
				foreach($relations as $relation) {
					$relation_ids[] = $relation->get_id();
				}
				$encoded_relations['incoming'] = $wprr_data_api->range()->encode_objects_as($relation_ids, 'relation');
			}
			
			$current_direction = $post->get_outgoing_direction();
			$types = $current_direction->get_types();
			foreach($types as $type) {
				$relations = $type->get_relations('*', false);
				$relation_ids = array();
				foreach($relations as $relation) {
					$relation_ids[] = $relation->get_id();
				}
				$encoded_relations['outgoing'] = $wprr_data_api->range()->encode_objects_as($relation_ids, 'relation');
			}
			
			$encoded_data->data['relations'] = $encoded_relations;
			
			//METODO: user relations
			//METODO: orders?
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Relations<br />");
		}
	}
?>