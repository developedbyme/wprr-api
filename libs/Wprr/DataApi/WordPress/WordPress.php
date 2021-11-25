<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\WordPress
	class WordPress {
		
		protected $_taxonomies = array();
		protected $_posts = array();
		protected $_users = array();
		protected $_fields_structures = array();

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
		
		public function get_user($id) {
			if(!isset($this->_users[$id])) {
				$new_user = new \Wprr\DataApi\WordPress\User();
				$new_user->setup($id);
				$this->_users[$id] = $new_user;
			}
			
			return $this->_users[$id];
		}
		
		public function get_fields_structure($type) {
			if(!isset($this->_fields_structures[$type])) {
				$new_fields_structure = new \Wprr\DataApi\WordPress\FieldsStructure();
				$new_fields_structure->setup($type);
				$this->_fields_structures[$type] = $new_fields_structure;
			}
			
			return $this->_fields_structures[$type];
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\WordPress<br />");
		}
	}
?>