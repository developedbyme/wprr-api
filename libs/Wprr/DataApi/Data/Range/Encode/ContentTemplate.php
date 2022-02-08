<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\ContentTemplate
	class ContentTemplate {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("ContentTemplate::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['name'] = $post->get_meta('name');
			$encoded_data->data['title'] = $post->get_meta('title');
			$encoded_data->data['content'] = $post->get_meta('content');
			
			//$encoded_data->data['type'] = //Relation: content-section-type
			
			
			/*
			$current_type = $setup_manager->create_data_type('template-position')->set_name('Template position');
			$current_type->add_field("name")->setup_meta_storage();
			$current_type->add_field("identifier")->setup_meta_storage();
			$current_type->add_field("description")->setup_meta_storage();
			*/
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ContentTemplate<br />");
		}
	}
?>