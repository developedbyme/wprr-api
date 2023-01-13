<?php
	namespace Wprr\DataApi\WordPress\Field;

	// \Wprr\DataApi\WordPress\Field\Field
	class Field {
		
		protected $_post = null;
		protected $_fields = null;
		
		function __construct() {
			
		}
		
		public function setup($post, $fields) {
			$this->_post = $post;
			$this->_fields = $fields;
			
			return $this;
		}
		
		public function get_post() {
			return $this->_post;
		}
		
		public function get_id() {
			return $this->get_post()->get_id();
		}
		
		public function get_name() {
			return $this->_post->get_meta('dbmtc_key');
		}
		
		public function get_storage_type() {
			global $wprr_data_api;
			$wp = $wprr_data_api->wordpress();
			return $this->get_post()->get_single_term_in($wp->get_taxonomy('dbm_relation')->get_term('field-storage'));
		}
		
		public function get_field_type() {
			global $wprr_data_api;
			$wp = $wprr_data_api->wordpress();
			return $this->get_post()->get_single_term_in($wp->get_taxonomy('dbm_relation')->get_term('field-type'));
		}
		
		public function format_value($value) {
			
			global $wprr_data_api;
			
			$field_type = $this->get_field_type();
			if($field_type) {
				switch($field_type->get_slug()) {
					case "name":
						if(!$value) {
							return array('firstName' => '', 'lastName' => '');
						}
						break;
					case "address":
						if(!$value) {
							return array("address1" => "", "address2" => "", "postCode" => "", "city" => "", "country" => "");
						}
						break;
					case "data-array":
						if(!$value) {
							return array();
						}
						break;
					case "json":
						if(!$value) {
							return null;
						}
						break;
					case "string":
					case "email":
					case "phoneNumber":
					case "date":
					case "date-time":
						return (string)$value;
					case "number":
						return (float)$value;
					case "boolean":
						return (boolean)$value;
					case "timestamp":
						return (int)$value;
					case "multiple-relation":
						$return_array = array();
						if($value) {
							$return_array = array_map(function($current_term_id) {
								global $wprr_data_api;
								if($current_term_id) {
									return $wprr_data_api->wordpress()->get_taxonomy('dbm_relation')->get_term_by_id($current_term_id);
								}
								return null;
							}, $value);
						}
						return $return_array;
					case "relation":
						//MENOTE: legacy where fields were marked as relation for relation-flag
						if(is_bool($value)) {
							return $value;
						}
						if($value) {
							return $wprr_data_api->wordpress()->get_taxonomy('dbm_relation')->get_term_by_id($value);
						}
						return null;
					case "dbm-type":
						if($value) {
							return $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term_by_id($value);
						}
						return null;
					default:
						$wprr_data_api->output()->log('Unknown field type '.$field_type->get_slug().' for value '.$value);
						break;
				}
			}
			
			/*
			//METODO
			self::add_term('dbm_relation:field-type/image', 'Image');
			self::add_term('dbm_relation:field-type/file', 'File');
			self::add_term('dbm_relation:field-type/multiple-files', 'Multiple files');
			self::add_term('dbm_relation:field-type/post-relation', 'Post relation');
			*/
			
			//METODO: use slug for type relation
			//$use_slug = (boolean)$field->get_meta('dbmtc_relation_use_slug');
			
			return $value;
		}
		
		public function get_value() {
			
			global $wprr_data_api;
			
			$return_value = null;
			
			$storage_type = $this->get_storage_type();
			if($storage_type) {
				switch($storage_type->get_slug()) {
					case "meta":
						$meta_field = $this->get_post()->get_meta('dbmtc_meta_name');
						$return_value = $this->_fields->get_post()->get_meta($meta_field);
						break;
					case "relation-flag":
						$relation_path = $this->get_post()->get_meta('dbmtc_relation_path');
						$return_value = $this->_fields->get_post()->has_term($wprr_data_api->wordpress()->get_taxonomy('dbm_relation')->get_term($relation_path));
						break;
					case "single-relation":
						$relation_path = $this->get_post()->get_meta('dbmtc_relation_path');
						$term = $this->_fields->get_post()->get_single_term_in_with_descendants($wprr_data_api->wordpress()->get_taxonomy('dbm_relation')->get_term($relation_path));
						if($term) {
							$return_value = $term->get_id();
						}
						else {
							$return_value = 0;
						}
						break;
					case "multiple-relation":
						$relation_path = $this->get_post()->get_meta('dbmtc_relation_path');
						$terms = $this->_fields->get_post()->get_terms_in_with_descendants($wprr_data_api->wordpress()->get_taxonomy('dbm_relation')->get_term($relation_path));
						$return_value = array_map(function($term) {return $term->get_id();}, $terms);
						break;
					default:
						$wprr_data_api->output()->log('Unknown field storage '.$storage_type->get_slug());
						break;
				}
			}
			
			
			return $this->format_value($return_value);
		}
		
		public function get_encoded_value() {
			global $wprr_data_api;
			
			$value = $this->get_value();
			$field_type = $this->get_field_type();
			if($field_type) {
				switch($field_type->get_slug()) {
					case "relation":
					case "dbm-type":
						//MENOTE: legacy where fields were marked as relation for relation-flag
						if($value instanceof \Wprr\DataApi\WordPress\TaxonomyTerm) {
							return $wprr_data_api->range()->encode_term($value);
						}
						break;
					case "multiple-relation":
						return array_map(function($current_term) {
							global $wprr_data_api;
							if($current_term instanceof \Wprr\DataApi\WordPress\TaxonomyTerm) {
								return $wprr_data_api->range()->encode_term($current_term);
							}
							return $value;
						}, $value);
				}
			}
			
			return $value;
		}
		
		public function get_translations() {
			return $this->get_post()->get_meta('dbmtc_value_translations');
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Field<br />");
		}
	}
?>