<?php
	namespace Wprr\OddCore\Filters;
	
	class FilterBase {
		
		protected $_filter_name = null;
		protected $_priority = 10;
		protected $_number_of_arguments = 3;
		
		function __construct() {
			//echo("\OddCore\Filters\FilterBase::__construct<br />");
			
			
		}
		
		public function perform_filter($output, $attributes, $content) {
			//echo("\OddCore\Filters\FilterBase::perform_filter<br />");
			
			return $output;
		}
		
		public function register() {
			//echo("\OddCore\Filters\FilterBase::register<br />");
			
			add_filter($this->_filter_name, array($this, 'perform_filter'), $this->_priority, $this->_number_of_arguments);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Filters\FilterBase<br />");
		}
	}
?>