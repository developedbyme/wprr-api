<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\Relation
	class Relation {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("Relation::select");
			
			global $wprr_data_api;
			
			$has_query = false;
			
			if(isset($data['type'])) {
				$has_query = true;
				$type_term = $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term($data['type']);
				//METODO: multiple types
				$query->include_only($type_term->get_ids());
			}
			
			if(isset($data['relation'])) {
				$has_query = true;
				$type_term = $wprr_data_api->wordpress()->get_taxonomy('dbm_relation')->get_term($data['relation']);
				//METODO: multiple types
				$query->include_only($type_term->get_ids());
			}
			
			if(!$has_query) {
				$query->include_only(array());
			}
		}
		
		public function filter($posts, $data) {
			//var_dump("Relation::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Relation<br />");
		}
	}
?>