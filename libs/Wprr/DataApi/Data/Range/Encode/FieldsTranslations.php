<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\FieldsTranslations
	class FieldsTranslations {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("FieldsTranslations::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$fields = $post->get_fields();
			
			$all_fields = $fields->get_all();
			
			$encoded_field_values = array();
			
			foreach($all_fields as $name => $field) {
				$value = $field->get_translations();
				$encoded_field_values[$name] = $value;
			}
			
			$encoded_data->data['fieldsTranslations'] = $encoded_field_values;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\FieldsTranslations<br />");
		}
	}
?>