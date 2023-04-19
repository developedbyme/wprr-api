<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\GlobalPointer
	class GlobalPointer {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("GlobalPointer::select");
			
			global $wprr_data_api;
			
			if(!isset($data['id'])) {
				throw(new \Exception('id not set'));
			}
			
			$query->include_term_by_path('dbm_type', 'global-item')->meta_query('identifier', $data['id']); 
			
		}
		
		public function filter($posts, $data) {
			//var_dump("GlobalPointer::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\GlobalPointer<br />");
		}
	}
?>