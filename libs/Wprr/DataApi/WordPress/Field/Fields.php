<?php
	namespace Wprr\DataApi\WordPress\Field;

	// \Wprr\DataApi\WordPress\Field\Fields
	class Fields {
		
		protected $_post = null;
		
		function __construct() {
			
		}
		
		public function setup($post) {
			$this->_post = $post;
			
			return $this;
		}
		
		public function get_post() {
			return $this->_post;
		}
		
		public function get_structures() {
			
			$return_array = array();
			
			global $wprr_data_api;
			$wp = $wprr_data_api->wordpress();
			
			$type_terms = $this->_post->get_taxonomy_terms('dbm_type');
			
			foreach($type_terms as $type_term) {
				$type = $type_term->get_path();
				
				$fields_structure = $wp->get_fields_structure($type);
				
				$return_array[] = $fields_structure;
			}
			
			return $return_array;
		}
		
		public function get_field($name) {
			
		}
		
		public function get_all() {
			//var_dump('Fields::get_all');
			
			global $wprr_data_api;
			$wp = $wprr_data_api->wordpress();
			
			$fields = array();
			
			$field_ids = $this->_post->get_incoming_direction()->get_type('field-for')->get_object_ids('internal-message-group-field');
			
			foreach($field_ids as $field_id) {
				$field = new \Wprr\DataApi\WordPress\Field\Field();
				$field->setup($wp->get_post($field_id), $this);
				
				$fields[$field->get_name()] = $field;
			}
			
			$structures = $this->get_structures();
			foreach($structures as $structure) {
				$field_templates = $structure->get_fields();
				foreach($field_templates as $name => $field_template) {
					if(!isset($fields[$name])) {
						$field = new \Wprr\DataApi\WordPress\Field\AbstractField();
						$field->setup($this->_post, $field_template);
						$fields[$name]  = $field;
					}
				}
			}
			
			return $fields;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Fields<br />");
		}
	}
?>