<?php
	namespace Wprr\Core\Admin\CustomPostTypes;
	
	// \Wprr\Core\Admin\CustomPostTypes\CustomPostTypePost
	class CustomPostTypePost {
		
		//METODO: create base object for posts and pages
		
		protected $_system_name = null;
		
		protected $_labels = array(
			'name' => 'Custom post types',
			'singular_name' => 'Custom post type',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Custom post type',
			'edit_item' => 'Edit Custom post type',
			'new_item' => 'New Custom post type',
			'all_items' => 'All Custom post types',
			'view_item' => 'View Custom post type',
			'search_items' => 'Search Custom post types',
			'not_found' => 'No custom post types found',
			'not_found_in_trash' => 'No custom post types found in trash',
			'parent_item_colon' => '',
			'menu_name' => 'Custom post types'
		);
		protected $_arguments = array();
		
		protected $_meta_boxes_after_title = array();
		protected $_meta_boxes_after_editor = array();
		
		protected $_owned_taxonomies = array();
		protected $_registered_taxonomies = array();
		
		public $javascript_files = array();
		public $javascript_data = array();
		public $css_files = array();
		
		function __construct() {
			//echo("\Core\Admin\CustomPostTypes\CustomPostTypePost::__construct<br />");
			
			$this->_arguments = array(
				'description' => null,
				'public' => true,
				'hierarchical' => false,
				'exclude_from_search' => false,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_nav_menus' => false,
				'show_in_menu' => true,
				'show_in_admin_bar' => true,
				'menu_position' => 5,
				'menu_icon' => null,
				'capability_type' => 'post',
				'hierarchical' => false,
				'can_export' => true,
				'supports' => array( 'title', 'editor', 'thumbnail'),
				'taxonomies' => array( 'category' ),
				'has_archive' => true,
				'rewrite' => array( 'slug' => $this->_system_name ),
				'query_var' => true
			);
			
			
			
			
			//'has_archive'           => 'wine',
			
		}
		
		public function set_argument($name, $value) {
			$this->_arguments[$name] = $value;
			
			return $this;
		}
		
		public function get_system_name() {
			return $this->_system_name;
		}
		
		public function set_names($system_name, $dipslay_name = null) {
			//echo("\Core\Admin\CustomPostTypes\CustomPostTypePost::set_names<br />");
			
			$this->_system_name = $system_name;
			
			if(isset($dipslay_name)) {
				$this->setup_labels_autonaming($dipslay_name);
			}
			
			return $this;
		}
		
		public function setup_labels_autonaming($name) {
			
			if(substr($name, -1) === 'y') {
				$multiple_name = substr($name, 0, -1).'ies';
			}
			else {
				$multiple_name = $name.'s';
			}
			
			$this->_labels = array(
				'name' => __( ucfirst($multiple_name), WPRR_TEXTDOMAIN ),
				'singular_name' => __( ucfirst($name), WPRR_TEXTDOMAIN ),
				'menu_name' => __( ucfirst($multiple_name), WPRR_TEXTDOMAIN ),
				'name_admin_bar' => __( ucfirst($name), WPRR_TEXTDOMAIN ),
				'add_new' => __( 'Add New', WPRR_TEXTDOMAIN ),
				'add_new_item' => __( 'Add New '.ucfirst($name), WPRR_TEXTDOMAIN ),
				'edit_item' => __( 'Edit '.ucfirst($name), WPRR_TEXTDOMAIN ),
				'new_item' => __( 'New '.ucfirst($name), WPRR_TEXTDOMAIN ),
				'all_items' => __( 'All '.ucfirst($multiple_name), WPRR_TEXTDOMAIN ),
				'view_item' => __( 'View '.ucfirst($name), WPRR_TEXTDOMAIN ),
				'search_items' => __( 'Search '.ucfirst($multiple_name), WPRR_TEXTDOMAIN ),
				'not_found' => __( 'No '.$multiple_name.' found', WPRR_TEXTDOMAIN ),
				'not_found_in_trash' => __( 'No '.$multiple_name.' found in trash', WPRR_TEXTDOMAIN ),
				'parent_item_colon'     => __( 'Parent '.ucfirst($name).':', WPRR_TEXTDOMAIN ),
				'archives'              => __( ucfirst($name).' Archives', WPRR_TEXTDOMAIN ),
				'edit_item'             => __( 'Edit '.ucfirst($name), WPRR_TEXTDOMAIN ),
				'update_item'           => __( 'Update '.ucfirst($name), WPRR_TEXTDOMAIN ),
				'view_item'             => __( 'View '.ucfirst($name), WPRR_TEXTDOMAIN ),
				'search_items'          => __( 'Search '.ucfirst($multiple_name), WPRR_TEXTDOMAIN ),
				'items_list'            => __( ucfirst($multiple_name).' list', WPRR_TEXTDOMAIN ),
				'items_list_navigation' => __( ucfirst($multiple_name).' list navigation', WPRR_TEXTDOMAIN ),
				'filter_items_list'     => __( 'Filter '.ucfirst($multiple_name).' list', WPRR_TEXTDOMAIN )
			);
			
			//'featured_image'        => __( 'Featured Image', WPRR_TEXTDOMAIN ),
			//'set_featured_image'    => __( 'Set featured image', WPRR_TEXTDOMAIN ),
			//'remove_featured_image' => __( 'Remove featured image', WPRR_TEXTDOMAIN ),
			//'use_featured_image'    => __( 'Use as featured image', WPRR_TEXTDOMAIN ),
			//'insert_into_item'      => __( 'Insert into item', WPRR_TEXTDOMAIN ),
			//'uploaded_to_this_item' => __( 'Uploaded to this item', WPRR_TEXTDOMAIN ),
		}
		
		public function get_owned_taxonomy($name) {
			return $this->_owned_taxonomies[$name];
		}
		
		public function add_owned_taxonomy($custom_taxonomy) {
			
			$name = $custom_taxonomy->get_system_name();
			
			$this->_owned_taxonomies[$name] = $custom_taxonomy;
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
		
		public function add_taxonomy($name) {
			
			$this->_registered_taxonomies[] = $name;
			
			return $this;
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
			//echo("\Core\Admin\CustomPostTypes\CustomPostTypePost::register<br />");
			
			$this->_arguments['labels'] = $this->_labels;
			
			register_post_type($this->_system_name, $this->_arguments);
			
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
			//echo("\Core\Admin\CustomPostTypes\CustomPostTypePost::output_after_title<br />");
			
			global $post;
			
			foreach($this->_meta_boxes_after_title as $meta_box) {
				$meta_box->output_with_nonce($post);
			}
		}
		
		public function output_after_editor() {
			//echo("\Core\Admin\CustomPostTypes\CustomPostTypePost::output_after_editor<br />");
			
			global $post;
			
			foreach($this->_meta_boxes_after_editor as $meta_box) {
				$meta_box->output_with_nonce($post);
			}
		}
		
		public function verify_and_save($post_id) {
			//echo("\Core\Admin\CustomPostTypes\CustomPostTypePost::verify_and_save<br />");
			
			foreach($this->_meta_boxes_after_title as $meta_box) {
				$meta_box->verify_and_save($post_id);
			}
			foreach($this->_meta_boxes_after_editor as $meta_box) {
				$meta_box->verify_and_save($post_id);
			}
		}
		
		public static function test_import() {
			echo("Imported \Core\Admin\CustomPostTypes\CustomPostTypePost<br />");
		}
	}
?>