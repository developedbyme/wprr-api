<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\ChildrenOf
	class ChildrenOf {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("ChildrenOf::select");
			
			global $wprr_data_api;
			
			if(!isset($data['fromIds'])) {
				throw(new \Exception('No ids specified'));
			}
			
			$ids = array_map(function($value) {return (int)$value;}, explode(',', $data['fromIds']));
			
			$query->with_parents($ids);
		}
		
		public function filter($posts, $data) {
			//var_dump("ChildrenOf::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ChildrenOf<br />");
		}
	}
?>