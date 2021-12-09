<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\DataSource
	class DataSource {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("DataSource::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['dataName'] = $post->get_meta('dataName');
			$encoded_data->data['data'] = $post->get_meta('data');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\DataSource<br />");
		}
	}
?>