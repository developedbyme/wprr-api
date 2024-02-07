<?php
	namespace Wprr\Core\Shortcode;
	
	// \Wprr\Core\Shortcode\ShortcodeFilter
	class ShortcodeFilter {
		
		protected $_keyword = null;
		
		function __construct() {
			//echo("\Core\Shortcode\ShortcodeFilter::__construct<br />");
			
			
		}
		
		public function set_keyword($keyword) {
			$this->_keyword = $keyword;
			
			return $this;
		}
		
		public function apply_shortcode($attributes, $content, $tag) {
			//echo("\Core\Shortcode\ShortcodeFilter::apply_shortcode<br />");
			
			return "<!-- MENOTE: shortcode not implemented -->";
		}
		
		public function register() {
			//echo("\Core\Shortcode\ShortcodeFilter::register<br />");
			
			add_shortcode($this->_keyword, array($this, 'apply_shortcode'));
		}
		
		public static function test_import() {
			echo("Imported \Core\Shortcode\ShortcodeFilter<br />");
		}
	}
?>