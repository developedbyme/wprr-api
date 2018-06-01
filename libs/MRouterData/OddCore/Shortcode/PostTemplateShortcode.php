<?php
	namespace Wprr\OddCore\Shortcode;
	
	use \Wprr\OddCore\Shortcode\ShortcodeFilter;
	
	// \Wprr\OddCore\Shortcode\PostTemplateShortcode
	class PostTemplateShortcode extends ShortcodeFilter {
		
		protected $_template_path = null;
		
		function __construct() {
			//echo("\OddCore\Shortcode\PostTemplateShortcode::__construct<br />");
			
			
		}
		
		public function set_template($path) {
			$this->_template_path = $path;
			
			return $this;
		}
		
		protected function get_post($attributes) {
			return get_post($attributes['id']);
		}
		
		public function apply_shortcode($attributes, $content, $tag) {
			//echo("\OddCore\Shortcode\PostTemplateShorctode::apply_shortcode<br />");
			
			global $post;
			$old_post = $post;
			
			ob_start();
			
			$post = $this->get_post($attributes);
			
			setup_postdata($post);
			$return_template = locate_template($this->_template_path, true, false);
			wp_reset_postdata();
			
			$return_value = ob_get_contents();
			ob_clean();
			
			if($return_template === '') {
				$return_value += '<!-- Template for shortcode not found -->';
			}
			
			$post = $old_post;
			
			return $return_value;
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Shortcode\PostTemplateShortcode<br />");
		}
	}
?>