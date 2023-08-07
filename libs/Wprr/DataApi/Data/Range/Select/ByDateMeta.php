<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\ByDateMeta
	class ByDateMeta {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("ByDateMeta::select");
			
			global $wprr_data_api;
			
			if(!isset($data['dateKey'])) {
				throw(new \Exception('dateKey not set'));
			}
			if(!isset($data['date'])) {
				throw(new \Exception('date not set'));
			}
			
			$query->meta_query_between_dates($data['dateKey'], $data['date'], $data['date']);
		}
		
		public function filter($posts, $data) {
			//var_dump("ByDateMeta::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ByDateMeta<br />");
		}
	}
?>