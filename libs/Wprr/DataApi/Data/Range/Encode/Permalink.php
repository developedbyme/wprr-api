<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Permalink
	class Permalink {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Permalink::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['permalink'] = SITE_URL.'/'.$post->get_link();
			
			//$encoded_data->data['meta'] = $post->get_meta('lagerkungen_facility_name');
			
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Permalink<br />");
		}
	}
?>