<?php
	namespace Wprr\Core\Admin\CustomPostTypes;
	
	// \Wprr\Core\Admin\CustomPostTypes\AlreadyRegisteredCustomPostTypePost
	class AlreadyRegisteredCustomPostTypePost {
		
		//METODO: create base object for posts and pages
		
		protected $_system_name = null;
		
		protected $_meta_boxes_after_title = array();
		protected $_meta_boxes_after_editor = array();
		
		protected $_owned_taxonomies = array();
		protected $_registered_taxonomies = array();
		
		public $javascript_files = array();
		public $javascript_data = array();
		public $css_files = array();
		
		function __construct() {
			//echo("\Core\Admin\CustomPostTypes\AlreadyRegisteredCustomPostTypePost::__construct<br />");
			
		}
		
		public function get_system_name() {
			return $this->_system_name;
		}
		
		public function set_names($system_name) {
			//echo("\Core\Admin\CustomPostTypes\AlreadyRegisteredCustomPostTypePost::set_names<br />");
			
			$this->_system_name = $system_name;
			
			return $this;
		}
		
		public function add_owned_taxonomy($custom_taxonomy) {
			
			$name = $custom_taxonomy->get_system_name();
			
			$this->_owned_taxonomies[$name] = $custom_taxonomy;
			$this->_registered_taxonomies[] = $name;
			
			return $this;
		}
		
		public function add_taxonomy($name) {
			
			$this->_registered_taxonomies[] = $name;
			
			return $this;
		}
		
		public function create_taxonomy($system_name, $display_name, $hierarchical) {
			$new_taxonomy = new \Wprr\Core\Admin\Taxonomies\CustomTaxonomy();
			$new_taxonomy->set_names($system_name, $display_name);
			$new_taxonomy->set_argument('hierarchical', $hierarchical);
			
			$this->add_owned_taxonomy($new_taxonomy);
			
			return $new_taxonomy;
		}
		
		public function add_meta_box_after_title($meta_box) {
			$this->_meta_boxes_after_title[] = $meta_box;
			
			return $this;
		}
		
		public function add_meta_box_after_editor($meta_box) {
			$this->_meta_boxes_after_editor[] = $meta_box;
			
			return $this;
		}
		
		public function register() {
			//echo("\Core\Admin\CustomPostTypes\AlreadyRegisteredCustomPostTypePost::register<br />");
			
			foreach($this->_owned_taxonomies as $name => $custom_taxonomy) {
				$custom_taxonomy->register();
			}
			foreach($this->_registered_taxonomies as $name) {
				register_taxonomy_for_object_type($name, $this->_system_name);
			}
			
			
		}
		
		public function enqueue_scripts_and_styles() {
			//echo("\Core\Admin\Pages\Page::enqueue_scripts_and_styles<br />");
			
			foreach($this->javascript_files as $id => $path) {
				wp_enqueue_script($id, $path);
			}
			
			foreach($this->javascript_data as $file_id => $data_array) {
				foreach($data_array as $object_id => $data) {
					wp_localize_script($file_id, $object_id, $data);
				}
			}
			
			foreach($this->css_files as $id => $path) {
				wp_enqueue_style($id, $path);
			}
		}
		
		public function add_javascript($id, $path) {
			if(isset($this->javascript_files[$id])) {
				//METODO: error message
			}
			$this->javascript_files[$id] = $path;
			
			return $this;
		}
		
		public function add_javascripts($scripts) {
			foreach($scripts as $id => $path) {
				$this->add_javascript($id, $path);
			}
			
			return $this;
		}
		
		public function add_javascript_data($id, $object_name, $data) {
			//echo("\Core\Admin\Pages\Page::add_javascript_data<br />");
			
			if(!isset($this->javascript_data[$id])) {
				//METODO: check that a script exists with that id
				$this->javascript_data[$id] = array();
			}
			$this->javascript_data[$id][$object_name] = $data;
			
			return $this;
		}
		
		public function add_css($id, $path) {
			if(isset($this->css_files[$id])) {
				//METODO: error message
			}
			$this->css_files[$id] = $path;
			
			return $this;
		}
		
		public function output_after_title() {
			//echo("\Core\Admin\CustomPostTypes\AlreadyRegisteredCustomPostTypePost::output_after_title<br />");
			
			global $post;
			
			foreach($this->_meta_boxes_after_title as $meta_box) {
				$meta_box->output_with_nonce($post);
			}
		}
		
		public function output_after_editor() {
			//echo("\Core\Admin\CustomPostTypes\AlreadyRegisteredCustomPostTypePost::output_after_editor<br />");
			
			global $post;
			
			foreach($this->_meta_boxes_after_editor as $meta_box) {
				$meta_box->output_with_nonce($post);
			}
		}
		
		public function verify_and_save($post_id) {
			//echo("\Core\Admin\CustomPostTypes\AlreadyRegisteredCustomPostTypePost::verify_and_save<br />");
			
			foreach($this->_meta_boxes_after_title as $meta_box) {
				$meta_box->verify_and_save($post_id);
			}
			foreach($this->_meta_boxes_after_editor as $meta_box) {
				$meta_box->verify_and_save($post_id);
			}
		}
		
		public static function test_import() {
			echo("Imported \Core\Admin\CustomPostTypes\AlreadyRegisteredCustomPostTypePost<br />");
		}
	}
?>