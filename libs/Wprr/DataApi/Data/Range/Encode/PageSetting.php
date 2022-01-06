<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\PageSetting
	class PageSetting {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("PageSetting::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['identifier'] = $post->get_meta('identifier');
			$encoded_data->data['data'] = $post->get_meta('data');
			
			$type = $post->get_incoming_direction()->get_type('for')->get_single_object_id('type/header-type');
			$encoded_data->data['headerType'] = $wprr_data_api->range()->encode_object_as($type, 'type');
			
			$type = $post->get_incoming_direction()->get_type('for')->get_single_object_id('type/hero-type');
			$encoded_data->data['heroType'] = $wprr_data_api->range()->encode_object_as($type, 'type');
			
			$type = $post->get_incoming_direction()->get_type('for')->get_single_object_id('type/footer-type');
			$encoded_data->data['footerType'] = $wprr_data_api->range()->encode_object_as($type, 'type');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\PageSetting<br />");
		}
	}
?>