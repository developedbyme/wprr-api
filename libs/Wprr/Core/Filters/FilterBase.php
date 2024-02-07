<?php
	namespace Wprr\Core\Filters;
	
	class FilterBase {
		
		protected $_filter_name = null;
		protected $_priority = 10;
		protected $_number_of_arguments = 3;
		
		function __construct() {
			//echo("\Core\Filters\FilterBase::__construct<br />");
			
			
		}
		
		public function perform_filter($output, $attributes, $content) {
			//echo("\Core\Filters\FilterBase::perform_filter<br />");
			
			return $output;
		}
		
		public function register() {
			//echo("\Core\Filters\FilterBase::register<br />");
			
			add_filter($this->_filter_name, array($this, 'perform_filter'), $this->_priority, $this->_number_of_arguments);
		}
		
		public static function test_import() {
			echo("Imported \Core\Filters\FilterBase<br />");
		}
	}
?>