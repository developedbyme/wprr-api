<?php
	namespace MRouterData\OddCore\Admin\MetaData;
	
	use \MRouterData\OddCore\Admin\MetaData\TermMetaDataField as TermMetaDataField;
	
	// \MRouterData\OddCore\Admin\MetaData\TermRelationMetaDataField
	class TermRelationMetaDataField extends TermMetaDataField {
		
		protected $_related_term = 'master_ingredient';
		
		function __construct() {
			//echo("\OddCore\Admin\MetaData\TermRelationMetaDataField::__construct<br />");
			
			
		}
		
		protected function output_option($value, $display_name, $selected_value) {
			
			
			echo('<option value="'.$value.'" '.selected($value, $selected_value).'>'.$display_name.'</option>');
			
			return ($value === $selected_value);
		}
		
		protected function output_field($value) {
			//echo("\OddCore\Admin\MetaData\TermRelationMetaDataField::output_field<br />");
			
			$terms = get_terms($this->_related_term, array(
				'hide_empty' => 0,
			));
			
			if($value === null) {
				$value = '';
			}
			
			$has_selected = false;
			if(!empty($terms) && !is_wp_error($terms)) {
				echo('<select name="'.$this->_field_name.'" id="'.$this->_field_name.'">');
				$has_selected |= $this->output_option('', '~Not set', $value);
				$has_selected |= $this->output_option(-1, '~None', $value);
				foreach($terms as $term) {
					$has_selected |= $this->output_option($term->term_id, $term->name, $value);
				}
				//METODO: Insert warning item
				echo('</select>');
				
				if(!$has_selected) {
					echo('<div class="error">No term is matching the saved value</div>');
				}
			}
			else {
				//METODO error handling
			}
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\MetaData\TermRelationMetaDataField<br />");
		}
	}
?>