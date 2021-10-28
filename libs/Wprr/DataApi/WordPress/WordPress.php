<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\WordPress
	class WordPress {
		
		protected $_taxonomies = array();
		protected $_posts = array();

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
		
		public function get_post($id) {
			if(!isset($this->_posts[$id])) {
				$new_post = new \Wprr\DataApi\WordPress\Post();
				$new_post->setup($id);
				$this->_posts[$id] = $new_post;
			}
			
			return $this->_posts[$id];
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\WordPress<br />");
		}
	}
?>