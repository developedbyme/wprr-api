<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Fields
	class Fields {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Fields::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$fields = $post->get_fields();
			
			$encoded_data->data['fieldsStructures'] = $wprr_data_api->range()->encode_fields_structures($fields->get_structures());
		
			$all_fields = $fields->get_all();
			
			$encoded_field_values = array();
			
			foreach($all_fields as $name => $field) {
				$encoded_field_values[$name] = $field->get_value();
			}
			
			$encoded_data->data['fieldValues'] = $encoded_field_values;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Fields<br />");
		}
	}
?>