<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\InDateRange
	class InDateRange {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("InDateRange::select");
			
			global $wprr_data_api;
			
			if(!isset($data['startDate'])) {
				throw(new \Exception('No startDate paramter'));
			}
			if(!isset($data['endDate'])) {
				throw(new \Exception('No endDate paramter'));
			}
			
			$query->in_date_range($data['startDate'], $data['endDate']);
		}
		
		public function filter($posts, $data) {
			//var_dump("InDateRange::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\InDateRange<br />");
		}
	}
?>