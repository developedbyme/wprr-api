<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\ObjectRelation
	class ObjectRelation {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("ObjectRelation::select");
			
			global $wprr_data_api;
			
			if(!isset($data['fromIds'])) {
				throw(new \Exception('fromIds not set'));
			}
			
			if(!isset($data['path'])) {
				throw(new \Exception('path not set'));
			}
			
			$from_ids = array_map(function($id) {return (int)$id;}, explode(',', $data['fromIds']));
			
			$from_posts = $wprr_data_api->wordpress()->get_posts($from_ids);
			
			$posts = \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationQuery::get_posts($from_posts, $data['path']);
			
			$ids = array_map(function($post) {return $post->get_id();}, $posts);
			
			$query->include_only($ids);
		}
		
		public function filter($posts, $data) {
			//var_dump("ObjectRelation::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ObjectRelation<br />");
		}
	}
?>