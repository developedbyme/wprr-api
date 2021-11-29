<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\AnyStatus
	class AnyStatus {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("AnyStatus::select");
			
			global $wprr_data_api;
			
			//METODO: check that user is allowed
			
			$query->include_all_statuses();
		}
		
		public function filter($posts, $data) {
			//var_dump("AnyStatus::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\AnyStatus<br />");
		}
	}
?>