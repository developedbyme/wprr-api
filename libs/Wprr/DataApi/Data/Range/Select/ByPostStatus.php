<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\ByPostStatus
	class ByPostStatus {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("ByPostStatus::select");
			
			global $wprr_data_api;
			
			$user = $wprr_data_api->user()->get_user_for_call($data);
			$is_ok = $user->is_trusted();
			if(!$is_ok) {
				throw(new \Exception('User '.$as_user.' is not allowed to get by post status'));
			}
			
			$query->set_status($data['status']);
		}
		
		public function filter($posts, $data) {
			//var_dump("ByPostStatus::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ByPostStatus<br />");
		}
	}
?>