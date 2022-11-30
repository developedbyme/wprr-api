<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\OpenSignupInvitation
	class OpenSignupInvitation {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("OpenSignupInvitation::select");
			
			global $wprr_data_api;
			
			$query->include_private();
			$query->include_only(array($data['id']));
			$query->meta_query('token', $data['token']);
			$query->include_term_by_path('dbm_type', 'signup-invite');
			
			//METODO: check that is open
		}
		
		public function filter($posts, $data) {
			//var_dump("OpenSignupInvitation::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\OpenSignupInvitation<br />");
		}
	}
?>