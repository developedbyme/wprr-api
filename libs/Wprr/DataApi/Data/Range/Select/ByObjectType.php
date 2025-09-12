<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\ByObjectType
	class ByObjectType {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("ByObjectType::select");
			
			global $wprr_data_api;
			
			if(isset($data['type'])) {
				$type_term = $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term($data['type']);
				//METODO: multiple types
				$query->include_only($type_term->get_ids());
			}
			else {
				throw(new \Exception('No type set'));
			}
		}
		
		public function filter($posts, $data) {
			//var_dump("ByObjectType::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ByObjectType<br />");
		}
	}
?>