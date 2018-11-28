<?php
	namespace Wprr\OddCore\Admin\MetaData;
	
	// \Wprr\OddCore\Admin\MetaData\TermMetaDataField
	class TermMetaDataField {
		
		protected $_type = 'ingredient';
		protected $_meta_key = '_master_ingredient';
		protected $_field_name = 'master-ingredient';
		protected $_nonce_field_name = 'master-ingredient-nonce';
		protected $_default_value = null;
		protected $_display_name = 'Master ingredient';
		protected $_save_empty_strings = true;
		
		function __construct() {
			//echo("\OddCore\Admin\MetaData\TermMetaDataField::__construct<br />");
			
			
		}
		
		public function register_hooks() {
			//echo("\OddCore\Admin\MetaData\TermMetaDataField::register_hooks<br />");
			
			add_action( $this->_type.'_add_form_fields', array($this, 'output_create_field'));
			add_action( $this->_type.'_edit_form_fields', array($this, 'output_with_nonce'));
			
			add_action('edit_'.$this->_type, array($this, 'verify_and_save'));
			add_action('create_'.$this->_type, array($this, 'verify_and_save'));
		}
		
		public function save($term_id) {
			//echo("\OddCore\Admin\MetaData\TermMetaDataField::save<br />");
			
			if(isset($_POST[$this->_field_name])) {
				if($_POST[$this->_field_name] !== '' || $this->_save_empty_strings) {
					$old_value = get_term_meta($term_id, $this->_meta_key, true);
					$new_value = $_POST[$this->_field_name];
					
					if($old_value !== $new_value) {
						update_term_meta($term_id, $this->_meta_key, $new_value);
					}
				}
			}
		}
		
		public function verify_and_save($term_id) {
			//echo("\OddCore\Admin\MetaData\TermMetaDataField::verify_and_save<br />");
			
			if (!isset($_POST[$this->_nonce_field_name]) || !wp_verify_nonce($_POST[$this->_nonce_field_name], basename(__FILE__))) {
				return;
			}
			
			$this->save($term_id);
		}
		
		protected function output_field($value) {
			//echo("\OddCore\Admin\MetaData\TermMetaDataField::output_field<br />");
			?>
				<input type="text" name="<?php echo($this->_field_name); ?>" id="<?php echo($this->_field_name); ?>" value="<?php echo esc_attr( $value ); ?>" />
			<?php
		}
		
		public function output_create_field() {
			wp_nonce_field(basename(__FILE__), $this->_nonce_field_name);
			
			$value = $this->_default_value;
			
			?>
				<div class="form-field">
					<label for="<?php echo($this->_field_name); ?>"><?php echo($this->_display_name); ?></label>
					<?php $this->output_field($value) ?>
				</div>
			<?php
		}
		
		public function output($term) {
			//echo("\OddCore\Admin\MetaData\TermMetaDataField::output<br />");
			
			$value = get_term_meta($term->term_id, $this->_meta_key, true);
			if(!isset($value)) {
				$value = $this->_default_value;
			}
			
			?>
				<tr class="form-field">
					<th scope="row"><label for="<?php echo($this->_field_name); ?>"><?php echo($this->_display_name); ?></label></th>
					<td>
						<?php $this->output_field($value) ?>
					</td>
				</tr>
			<?php
		}
		
		public function output_with_nonce($term) {
			//echo("\OddCore\Admin\MetaData\TermMetaDataField::output_with_nonce<br />");
			
			wp_nonce_field(basename(__FILE__), $this->_nonce_field_name);
			$this->output($term);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\MetaData\TermMetaDataField<br />");
		}
	}
?>