<?php
	namespace Wprr\DataApi\Data\Range\Encode;
	
	class OrderItems {
		
		function __construct() {
			//echo("\Wprr\DataApi\Data\Range\Encode\OrderItems::__construct<br />");
			
		}
		
		public function prepare($ids) {
			
		}
		
		public function encode($id) {
			//var_dump("OrderItems::encode");
			
			global $wprr_data_api;
			
			$wprr_data_api->range()->encode_object_as($id, 'order/items');
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Data\Range\Encode\OrderItems<br />");
		}
	}
?>