<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\ByTrigger
	class ByTrigger {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("ByTrigger::select");
			
			global $wprr_data_api;
			
			if(!isset($data['trigger'])) {
				throw(new \Exception('trigger not set'));
			}
			
			$subquery = $wprr_data_api->range()->new_query()->include_private()->include_term_by_path('dbm_type', 'type/trigger-type')->meta_query('identifier', $data['trigger']); 
			
			$from_ids = $subquery->get_ids();
			$from_posts = $wprr_data_api->wordpress()->get_posts($from_ids);
			
			$posts = \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationQuery::get_posts($from_posts, 'out:for:trigger,out:for:*');
			
			$ids = array_map(function($post) {return $post->get_id();}, $posts);
			
			$query->include_only($ids);
		}
		
		public function filter($posts, $data) {
			//var_dump("ByTrigger::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ByTrigger<br />");
		}
	}
?>