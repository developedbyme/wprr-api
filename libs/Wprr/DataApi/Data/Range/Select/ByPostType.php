<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\ByPostType
	class ByPostType {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("ByPostType::select");
			
			global $wprr_data_api;
			
			$query->set_post_type($data['postType']);
		}
		
		public function filter($posts, $data) {
			//var_dump("ByPostType::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ByPostType<br />");
		}
	}
?>