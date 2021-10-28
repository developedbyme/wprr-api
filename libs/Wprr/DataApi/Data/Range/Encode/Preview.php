<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Preview
	class Preview {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Preview::encode");
			
			global $wprr_data_api;
			
			$wprr_data_api->range()->encode_object_as($id, 'postTitle');
			$wprr_data_api->range()->encode_object_as($id, 'permalink');
			$wprr_data_api->range()->encode_object_as($id, 'featuredImage');
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Preview<br />");
		}
	}
?>