<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\Products
	class Products {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("Products::select");
			
			global $wprr_data_api;
			
			$query->set_post_type('product');
		}
		
		public function filter($posts, $data) {
			//var_dump("Products::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Products<br />");
		}
	}
?>