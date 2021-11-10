<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\PageSettings
	class PageSettings {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("PageSettings::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$setting_id = $post->get_incoming_direction()->get_type('for')->get_single_object_id('settings/page-settings');
			$encoded_data->data['pageSettings'] = $wprr_data_api->range()->encode_object_as($setting_id, 'pageSetting');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\PageSettings<br />");
		}
	}
?>