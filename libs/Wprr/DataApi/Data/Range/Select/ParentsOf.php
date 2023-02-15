<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\ParentsOf
	class ParentsOf {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("ParentsOf::select");
			
			global $wprr_data_api;
			
			if(!isset($data['fromIds'])) {
				throw(new \Exception('No ids specified'));
			}
			
			$ids = array_map(function($value) {return (int)$value;}, explode(',', $data['fromIds']));
			$parents = array();
			
			foreach($ids as $id) {
				$post = $wprr_data_api->wordpress()->get_post($id);
				
				$parent = $post->get_parent();
				while($parent) {
				
					$parents[] = $parent->get_id();
				
					$parent = $parent->get_parent();
				}
			}
			
			$query->include_only($parents);
		}
		
		public function filter($posts, $data) {
			//var_dump("ParentsOf::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ParentsOf<br />");
		}
	}
?>