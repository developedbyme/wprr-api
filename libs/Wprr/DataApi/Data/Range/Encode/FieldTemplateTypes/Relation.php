<?php
	namespace Wprr\DataApi\Data\Range\Encode\FieldTemplateTypes;

	// \Wprr\DataApi\Data\Range\Encode\FieldTemplateTypes\Relation
	class Relation {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Relation::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$relation_path = $post->get_meta('dbmtc_relation_path');
			if($relation_path) {
				$encoded_data->data['relationPath'] = $wprr_data_api->range()->encode_term($wprr_data_api->wordpress()->get_taxonomy('dbm_relation')->get_term($relation_path));
			}
			else {
				$encoded_data->data['relationPath'] = null;
			}
			
			$subtree = $post->get_meta('subtree');
			if($subtree) {
				$encoded_data->data['subtree'] = $wprr_data_api->range()->encode_term($wprr_data_api->wordpress()->get_taxonomy('dbm_relation')->get_term($subtree));
			}
			else {
				$encoded_data->data['subtree'] = null;
			}
			
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Relation<br />");
		}
	}
?>