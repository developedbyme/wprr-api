<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\ObjectTypes
	class ObjectTypes {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("ObjectTypes::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['objectTypes'] = $wprr_data_api->range()->encode_terms($post->get_taxonomy_terms('dbm_type'));
			
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ObjectTypes<br />");
		}
	}
?>