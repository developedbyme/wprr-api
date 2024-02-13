<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\MyObjectRelation
	class MyObjectRelation {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("MyObjectRelation::select");
			
			global $wprr_data_api;
			
			$user = $wprr_data_api->user()->get_user_for_call($data);
			
			if(!isset($data['path'])) {
				throw(new \Exception('path not set'));
			}
			
			$posts = \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationQuery::get_posts(array($user), $data['path']);
			
			$ids = array_map(function($post) {return $post->get_id();}, $posts);
			
			$query->include_only($ids);
			
		}
		
		public function filter($posts, $data) {
			//var_dump("MyObjectRelation::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\MyObjectRelation<br />");
		}
	}
?>