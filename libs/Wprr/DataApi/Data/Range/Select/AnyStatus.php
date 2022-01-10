<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\AnyStatus
	class AnyStatus {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("AnyStatus::select");
			
			global $wprr_data_api;
			
			//METODO: check that user is allowed
			$user = $wprr_data_api->user()->get_user_for_call($data);
			$is_ok = in_array('administrator', $user->get_roles());
			if(!$is_ok) {
				throw(new \Exception('User '.$as_user.' is not allowed to get anyStatus'));
			}
			
			$query->include_all_statuses();
		}
		
		public function filter($posts, $data) {
			//var_dump("AnyStatus::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\AnyStatus<br />");
		}
	}
?>