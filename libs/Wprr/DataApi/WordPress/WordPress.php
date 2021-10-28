<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\WordPress
	class WordPress {
		
		protected $_taxonomies = array();

		function __construct() {
			
		}
		
		public function get_taxonomy($name) {
			if(!isset($this->_taxonomies[$name])) {
				$new_taxonomy = new \Wprr\DataApi\WordPress\Taxonomy();
				$new_taxonomy->setup($name);
				$this->_taxonomies[$name] = $new_taxonomy;
			}
			
			return $this->_taxonomies[$name];
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\WordPress<br />");
		}
	}
?>