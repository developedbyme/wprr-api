<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\GlobalObjectRelation
	class GlobalObjectRelation {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("GlobalObjectRelation::select");
			
			global $wprr_data_api;
			
			if(!isset($data['id'])) {
				throw(new \Exception('id not set'));
			}
			
			if(!isset($data['path'])) {
				throw(new \Exception('path not set'));
			}
			
			$subquery = $wprr_data_api->range()->new_query()->include_private()->include_term_by_path('dbm_type', 'global-item')->meta_query('identifier', $data['id']); 
			
			$from_ids = $subquery->get_ids();
			$from_posts = \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationQuery::get_posts($wprr_data_api->wordpress()->get_posts($from_ids), 'out:pointing-to:*');
			
			$posts = \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationQuery::get_posts($from_posts, $data['path']);
			
			$ids = array_map(function($post) {return $post->get_id();}, $posts);
			
			$query->include_only($ids);
		}
		
		public function filter($posts, $data) {
			//var_dump("GlobalObjectRelation::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\GlobalObjectRelation<br />");
		}
	}
?>