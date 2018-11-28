<?php
	namespace Wprr\OddCore\Admin\Lists;
	
	// \Wprr\OddCore\Admin\Lists\EditList
	class EditList {
		
		protected $_type = 'ingredient';
		protected $_columns = array();
		
		function __construct() {
			//echo("\OddCore\Admin\Lists\EditList::__construct<br />");
			
			$this->_columns["master-ingredient"] = new \Wprr\OddCore\Admin\Lists\EditListColumn();
		}
		
		public function register_list() {
			//echo("\OddCore\Admin\Lists\EditList::register_list<br />");
			
			add_filter('manage_edit-'.$this->_type.'_columns', array($this, 'add_columns'));
			add_filter('manage_'.$this->_type.'_custom_column', array($this, 'output_column'), 10, 3);
			
		}
		
		public function add_columns($columns) {
			//echo("\OddCore\Admin\Lists\EditList::add_columns<br />");
			
			foreach($this->_columns as $key => $column) {
				$columns[$key] = $column->name;
			}
			
			return $columns;
		}
		
		public function output_column($out, $column, $term_id) {
			//echo("\OddCore\Admin\Lists\EditList::output_column<br />");
			
			return $this->_columns[$column]->output($out, $term_id);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\Lists\EditList<br />");
		}
	}
?>