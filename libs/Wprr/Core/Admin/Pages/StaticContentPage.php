<?php
	namespace Wprr\Core\Admin\Pages;
	
	use Wprr\Core\Admin\Pages\Page as Page;
	
	class StaticContentPage extends Page {
		
		protected $_content = null;
		
		function __construct() {
			//echo("\Core\Admin\Pages\StaticContentPage::__construct<br />");
			
			
		}
		
		public function set_content($content) {
			//echo("\Core\Admin\Pages\StaticContentPage::set_content<br />");
			
			$this->_content = $content;
			
			return $this;
		}
		
		public function output() {
			//echo("\Core\Admin\Pages\StaticContentPage::output<br />");
			
			echo($this->_content);
		}
		
		public static function test_import() {
			echo("Imported \Core\Admin\Pages\StaticContentPage<br />");
		}
	}
?>