<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Breadcrumb
	class Breadcrumb {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Breadcrumb::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['breadcrumb'] = $post->get_post_content();
			
			$parents = array();
			
			$parent = $post->get_parent();
			while($parent) {
				
				$parents[] = $wprr_data_api->range()->encode_object_as($parent->get_id(), 'postTitle');
				$wprr_data_api->range()->encode_object_as($parent->get_id(), 'permalink');
				
				$parent = $parent->get_parent();
			}
			
			$encoded_data->data['breadcrumb'] = array_reverse($parents);
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Breadcrumb<br />");
		}
	}
?>