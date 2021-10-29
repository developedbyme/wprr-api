<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\IdSelection
	class IdSelection {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("IdSelection::select");
			
			global $wprr_data_api;
			
			$has_query = false;
			
			if(!isset($data['ids'])) {
				throw(new \Exception('No ids specified'));
			}
			
			$ids = array_map(function($value) {return (int)$value;}, explode(',', $data['ids']));
			$query->include_only($ids);
		}
		
		public function filter($posts, $data) {
			//var_dump("IdSelection::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\IdSelection<br />");
		}
	}
?>