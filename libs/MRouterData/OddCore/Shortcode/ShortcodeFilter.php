<?php
	namespace MRouterData\OddCore\Shortcode;
	
	// \MRouterData\OddCore\Shortcode\ShortcodeFilter
	class ShortcodeFilter {
		
		protected $_keyword = null;
		
		function __construct() {
			//echo("\OddCore\Shortcode\ShortcodeFilter::__construct<br />");
			
			
		}
		
		public function set_keyword($keyword) {
			$this->_keyword = $keyword;
			
			return $this;
		}
		
		public function apply_shortcode($attributes, $content, $tag) {
			//echo("\OddCore\Shortcode\ShortcodeFilter::apply_shortcode<br />");
			
			return "<!-- MENOTE: shortcode not implemented -->";
		}
		
		public function register() {
			//echo("\OddCore\Shortcode\ShortcodeFilter::register<br />");
			
			add_shortcode($this->_keyword, array($this, 'apply_shortcode'));
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Shortcode\ShortcodeFilter<br />");
		}
	}
?>