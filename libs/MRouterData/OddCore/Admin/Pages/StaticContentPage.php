<?php
	namespace MRouterData\OddCore\Admin\Pages;
	
	use MRouterData\OddCore\Admin\Pages\Page as Page;
	
	class StaticContentPage extends Page {
		
		protected $_content = null;
		
		function __construct() {
			//echo("\OddCore\Admin\Pages\StaticContentPage::__construct<br />");
			
			
		}
		
		public function set_content($content) {
			//echo("\OddCore\Admin\Pages\StaticContentPage::set_content<br />");
			
			$this->_content = $content;
			
			return $this;
		}
		
		public function output() {
			//echo("\OddCore\Admin\Pages\StaticContentPage::output<br />");
			
			echo($this->_content);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\Pages\StaticContentPage<br />");
		}
	}
?>