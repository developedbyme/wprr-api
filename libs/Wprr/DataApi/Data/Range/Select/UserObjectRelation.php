<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\UserObjectRelation
	class UserObjectRelation {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("UserObjectRelation::select");
			
			global $wprr_data_api;
			
			if(!isset($data['id'])) {
				throw(new \Exception('fromIds not set'));
			}
			
			if(!isset($data['relationType'])) {
				throw(new \Exception('relationType not set'));
			}
			
			if(!isset($data['objectType'])) {
				throw(new \Exception('objectType not set'));
			}
			
			$user = $wprr_data_api->wordpress()->get_user($data['id']);
			
			$ids = $user->get_relation_type($data['relationType'])->get_object_ids($data['objectType']);
			
			$query->include_only($ids);
		}
		
		public function filter($posts, $data) {
			//var_dump("UserObjectRelation::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\UserObjectRelation<br />");
		}
	}
?>