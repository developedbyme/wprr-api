<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\PostRelation
	class PostRelation {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("PostRelation::select");
			
			global $wprr_data_api;
			
			$has_query = false;
			
			if(isset($data['postRelation'])) {
				$has_query = true;
				
				$relations = explode(',', $data['postRelation']);
				foreach($relations as $relation) {
					$temp_array = explode(':', $relation);
					$group = $temp_array[0];
					$owner_id = (int)$temp_array[1];
					
					$query->include_post_relation_by_path($wprr_data_api->wordpress()->get_post($owner_id), $group);
				}
			}
			
			if(!$has_query) {
				throw(new \Exception('No postRelation parameter'));
			}
		}
		
		public function filter($posts, $data) {
			//var_dump("PostRelation::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\PostRelation<br />");
		}
	}
?>