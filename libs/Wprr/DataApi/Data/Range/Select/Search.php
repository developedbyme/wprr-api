<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\Search
	class Search {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("Search::select");
			
			global $wprr_data_api;
			
			if(!isset($data['search'])) {
				throw(new \Exception('search not set'));
			}
			
			$db = $wprr_data_api->database();
			$sub_query = 'SELECT id FROM '.DB_TABLE_PREFIX.'posts WHERE LOWER(post_title) LIKE \'%'.strtolower($db->escape($data['search'])).'%\'';
			$results = $db->query_without_storage($sub_query);
			
			$ids = array_map(function($result) {return (int)$result['id'];}, $results);
			
			$query->include_only($ids);
		}
		
		public function filter($posts, $data) {
			//var_dump("Search::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Search<br />");
		}
	}
?>