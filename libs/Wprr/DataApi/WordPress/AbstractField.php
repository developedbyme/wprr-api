<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\AbstractField
	class AbstractField {
		
		protected $_post = null;
		protected $_field_template = null;
		
		function __construct() {
			
		}
		
		public function setup($post, $field_template) {
			$this->_post = $post;
			$this->_field_template = $field_template;
			
			return $this;
		}
		
		public function get_name() {
			return $this->_field_template->get_name();
		}
		
		public function get_value() {
			return null;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\AbstractField<br />");
		}
	}
?>