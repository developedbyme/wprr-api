<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\IncludeDraft
	class IncludeDraft {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("IncludeDraft::select");
			
			global $wprr_data_api;
			
			//METODO: check that user is allowed
			$user = $wprr_data_api->user()->get_user_for_call($data);
			$is_ok = $user->is_trusted();
			if(!$is_ok) {
				throw(new \Exception('User '.$as_user.' is not allowed to get draft'));
			}
			
			$query->include_draft();
		}
		
		public function filter($posts, $data) {
			//var_dump("IncludeDraft::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\IncludeDraft<br />");
		}
	}
?>