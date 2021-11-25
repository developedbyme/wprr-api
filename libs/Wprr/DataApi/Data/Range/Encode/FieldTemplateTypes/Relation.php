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
			
			$encoded_data->data['relationPath'] = $post->get_meta('dbmtc_relation_path');
			$encoded_data->data['subtree'] = $post->get_meta('subtree');
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Relation<br />");
		}
	}
?>