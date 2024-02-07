<?php
	namespace Wprr\Core\Admin\MetaData;
	
	use \Wprr\Core\Admin\MetaData\MetaField;
	
	// \Wprr\Core\Admin\MetaData\ArrayMetaField
	class ArrayMetaField extends MetaField {
		
		function __construct() {
			//echo("\Core\Admin\MetaData\ArrayMetaField::__construct<br />");
			
			
		}
		
		public function save($post_id) {
			//echo("\Core\Admin\MetaData\ArrayMetaField::save<br />");
			
			if(isset($_POST[$this->_field_name])) {
				if($_POST[$this->_field_name] !== '') {
					
					$new_value_array = explode(',', $_POST[$this->_field_name]);
					
					delete_post_meta($post_id, $this->_meta_key);
					
					foreach($new_value_array as $new_value) {
						add_post_meta($post_id, $this->_meta_key, $new_value);
					}
				}
				else if($this->_save_empty_strings) {
					delete_post_meta($post_id, $this->_meta_key);
				}
			}
		}
		
		public function get_value($post) {
			$value = get_post_meta($post->ID, $this->_meta_key);
			if(!isset($value)) {
				$value = $this->_default_value;
			}
			
			return $value;
		}
		
		public function output($post) {
			//echo("\Core\Admin\MetaData\ArrayMetaField::output<br />");
			
			//METODO
		}
		
		public static function test_import() {
			echo("Imported \Core\Admin\MetaData\ArrayMetaField<br />");
		}
	}
?>