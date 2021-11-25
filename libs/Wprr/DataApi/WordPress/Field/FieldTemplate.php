<?php
	namespace Wprr\DataApi\WordPress\Field;

	// \Wprr\DataApi\WordPress\Field\FieldTemplate
	class FieldTemplate {
		
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
		
		public function get_id() {
			return $this->get_post()->get_id();
		}
		
		public function get_name() {
			return $this->get_post()->get_meta('dbmtc_key');
		}
		
		public function get_for_type() {
			
			$type_id = (int)$this->get_post()->get_meta('dbmtc_for_type');
			
			$term = $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term_by_id($type_id);
			
			return $term;
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
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\FieldTemplate<br />");
		}
	}
?>