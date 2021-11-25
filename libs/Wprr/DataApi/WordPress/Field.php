<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\Field
	class Field {
		
		protected $_post = null;
		
		function __construct() {
			
		}
		
		public function setup($post) {
			$this->_post = $post;
			
			return $this;
		}
		
		public function get_name() {
			return $this->_post->get_meta('dbmtc_key');
		}
		
		public function get_value() {
			return $this->_post->get_meta('dbmtc_value');
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Field<br />");
		}
	}
?>