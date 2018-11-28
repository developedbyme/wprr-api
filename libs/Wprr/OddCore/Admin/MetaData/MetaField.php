<?php
	namespace Wprr\OddCore\Admin\MetaData;
	
	// \Wprr\OddCore\Admin\MetaData\MetaField
	class MetaField {
		
		protected $_field_name = null;
		protected $_meta_key = null;
		protected $_default_value = null;
		protected $_save_empty_strings = true;
		
		function __construct() {
			//echo("\OddCore\Admin\MetaData\MetaField::__construct<br />");
			
			
		}
		
		public function set_name($meta_key) {
			
			$this->_field_name = $meta_key;
			$this->_meta_key = $meta_key;
			
			return $this;
		}
		
		public function set_default_value($default_value) {
			
			$this->_default_value = $default_value;
			
			return $this;
		}
		
		public function save($post_id) {
			//echo("\OddCore\Admin\MetaData\MetaField::save<br />");
			
			if(isset($_POST[$this->_field_name])) {
				if($_POST[$this->_field_name] !== '' || $this->_save_empty_strings) {
					$old_value = get_post_meta($post_id, $this->_meta_key, true);
					$new_value = $_POST[$this->_field_name];
					
					if($old_value !== $new_value) {
						update_post_meta($post_id, $this->_meta_key, $new_value);
					}
				}
			}
		}
		
		public function get_field_name() {
			return $this->_field_name;
		}
		
		public function get_meta_key() {
			return $this->_meta_key;
		}
		
		public function get_value($post) {
			$value = get_post_meta($post->ID, $this->_meta_key, true);
			if(empty($value)) {
				$value = $this->_default_value;
			}
			
			return $value;
		}
		
		public function output($post) {
			//echo("\OddCore\Admin\MetaData\MetaField::output<br />");
			
			//METODO
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\MetaData\MetaField<br />");
		}
	}
?>