<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\TypeObjectRelation
	class TypeObjectRelation {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("TypeObjectRelation::select");
			
			global $wprr_data_api;
			
			if(!isset($data['type'])) {
				throw(new \Exception('type not set'));
			}
			
			if(!isset($data['id'])) {
				throw(new \Exception('id not set'));
			}
			
			if(!isset($data['path'])) {
				throw(new \Exception('path not set'));
			}
			
			$subquery = $wprr_data_api->range()->new_query()->include_private()->include_term_by_path('dbm_type', $data['type'])->meta_query('identifier', $data['id']); 
			
			$from_ids = $subquery->get_ids();
			$from_posts = $wprr_data_api->wordpress()->get_posts($from_ids);
			
			$posts = \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationQuery::get_posts($from_posts, $data['path']);
			
			$ids = array_map(function($post) {return $post->get_id();}, $posts);
			
			$query->include_only($ids);
		}
		
		public function filter($posts, $data) {
			//var_dump("TypeObjectRelation::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\TypeObjectRelation<br />");
		}
	}
?>