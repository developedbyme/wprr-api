<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\Posts
	class Posts {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("Posts::select");
			
			global $wprr_data_api;
			
			$query->set_post_type('post');
		}
		
		public function filter($posts, $data) {
			//var_dump("Posts::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Posts<br />");
		}
	}
?>