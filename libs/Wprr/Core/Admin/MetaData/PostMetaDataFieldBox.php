<?php
	namespace Wprr\Core\Admin\MetaData;
	
	use \Wprr\Core\Admin\MetaData\PostMetaDataBox;
	
	// \Wprr\Core\Admin\MetaData\PostMetaDataFieldBox
	class PostMetaDataFieldBox extends PostMetaDataBox{
		
		protected $_meta_key = null;
		protected $_field_name = null;
		protected $_default_value = null;
		protected $_save_empty_strings = true;
		
		function __construct() {
			//echo("\Core\Admin\MetaData\PostMetaDataFieldBox::__construct<br />");
			
			
		}
		
		public function set_meta_key($name) {
			$this->_meta_key = $name;
			$this->_field_name = $name;
			$this->set_nonce_name($name.'-nonce');
			
			return $this;
		}
		
		public function save($post_id) {
			//echo("\Core\Admin\MetaData\PostMetaDataFieldBox::save<br />");
			
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
		
		protected function output_content($post) {
			//echo("\Core\Admin\MetaData\PostMetaDataFieldBox::output_content<br />");
			
			$value = get_post_meta($post->ID, $this->_meta_key, true);
			if(!isset($value)) {
				$value = $this->_default_value;
			}
			
			?>
				<input type="text" name="<?php echo($this->_field_name); ?>" id="<?php echo($this->_field_name); ?>" class="widefat" value="<?php echo esc_attr( $value ); ?>" />
			<?php
		}
		
		public static function test_import() {
			echo("Imported \Core\Admin\MetaData\PostMetaDataFieldBox<br />");
		}
	}
?>