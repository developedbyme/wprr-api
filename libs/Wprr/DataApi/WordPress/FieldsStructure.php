<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\FieldsStructure
	class FieldsStructure {
		
		protected $_type = null;
		protected $_field_templates = null;
		
		function __construct() {
			
		}
		
		public function setup($type) {
			$this->_type = $type;
			
			return $this;
		}
		
		public function get_identifier() {
			return 'fieldsStructure/'.$this->get_type();
		}
		
		public function get_type() {
			return $this->_type;
		}
		
		public function get_fields() {
			global $wprr_data_api;
			
			if(!$this->_field_templates) {
				$this->_field_templates = array();
				
				$query = $wprr_data_api->database()->new_select_query();
				
				$term = $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term('field-template');
				$type_term = $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term($this->_type);
				
				$field_template_ids = $query->include_private()->include_term($term)->meta_query('dbmtc_for_type', $type_term->get_id())->get_ids();
				
				foreach($field_template_ids as $field_template_id) {
					$field_template_post = $wprr_data_api->wordpress()->get_post($field_template_id);
					
					$field_template = new \Wprr\DataApi\WordPress\FieldTemplate();
					$field_template->setup($field_template_post);
					
					$name = $field_template->get_key();
					$this->_field_templates[$name] = $field_template;
				}
			}
			
			return $this->_field_templates;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\FieldsStructure<br />");
		}
	}
?>