<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\PageDataSources
	class PageDataSources {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("PageDataSources::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$source_ids = $post->get_incoming_direction()->get_type('for')->get_object_ids('settings/data-source');
			$encoded_data->data['dataSources'] = $wprr_data_api->range()->encode_objects_as($source_ids, 'dataSource');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\PageDataSources<br />");
		}
	}
?>