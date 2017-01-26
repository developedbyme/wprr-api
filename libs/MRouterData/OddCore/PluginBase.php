<?php
	namespace MRouterData\OddCore;
	
	// \MRouterData\OddCore\PluginBase
	class PluginBase {
		
		protected $_pages = null;
		protected $_custom_post_types = null;
		protected $_lists = null;
		protected $_ajax_api_end_points = null;
		protected $_rest_api_end_points = null;
		protected $_shortcodes = null;
		protected $_meta_boxes = null;
		protected $_filters = null;
		protected $_additional_hooks = null;
		
		public $javascript_files = array();
		public $css_files = array(); //METODO
		
		function __construct() {
			//echo("\MRouterData\OddCore\PluginBase::__construct<br />");
			
			$this->register_hooks();
		}
		
		protected function register_lists() {
			//echo("\MRouterData\OddCore\PluginBase::register_lists<br />");
			
			$this->_lists = array();
			
			return $this->_lists;
		}
		
		protected function add_page($page) {
			$this->_pages["toplevel_page_".$page->get_system_name()] = $page;
		}
		
		protected function create_pages() {
			echo("\MRouterData\OddCore\PluginBase::create_pages<br />");
			
			//MENOTE: should be overridden
		}
		
		protected function register_pages() {
			//echo("\MRouterData\OddCore\PluginBase::register_pages<br />");
			
			$this->_pages = array();
			$this->create_pages();
			
			return $this->_pages;
		}
		
		protected function add_additional_hook($additional_hook) {
			//echo("\MRouterData\OddCore\PluginBase::add_additional_hook<br />");
			
			$this->_additional_hooks[] = $additional_hook;
		}
		
		protected function create_additional_hooks() {
			//echo("\MRouterData\OddCore\PluginBase::create_additional_hooks<br />");
			
			//MENOTE: should be overridden
		}
		
		protected function register_additional_hooks() {
			//echo("\MRouterData\OddCore\PluginBase::register_additional_hooks<br />");
			
			$this->_additional_hooks = array();
			
			$this->create_additional_hooks();
			
			return $this->_additional_hooks;
		}
		
		protected function get_additional_hooks() {
			//echo("\MRouterData\OddCore\PluginBase::get_additional_hooks<br />");
			
			if(!isset($this->_additional_hooks)) {
				$this->register_additional_hooks();
			}
			return $this->_additional_hooks;
		}
		
		protected function add_custom_post_type($custom_post_type) {
			//echo("\MRouterData\OddCore\PluginBase::add_custom_post_type<br />");
			
			$this->_custom_post_types[$custom_post_type->get_system_name()] = $custom_post_type;
		}
		
		protected function create_custom_post_types() {
			//echo("\MRouterData\OddCore\PluginBase::create_custom_post_types<br />");
			
			//MENOTE: should be overridden
		}
		
		protected function register_custom_post_types() {
			//echo("\MRouterData\OddCore\PluginBase::register_custom_post_types<br />");
			
			$this->_custom_post_types = array();
			
			$this->create_custom_post_types();
			
			return $this->_custom_post_types;
		}
		
		protected function add_filter($filter) {
			//echo("\MRouterData\OddCore\PluginBase::add_filter<br />");
			
			$this->_filters[] = $filter;
		}
		
		protected function create_filters() {
			//echo("\MRouterData\OddCore\PluginBase::create_filters<br />");
			
			//MENOTE: should be overridden
		}
		
		protected function register_filters() {
			//echo("\MRouterData\OddCore\PluginBase::register_filters<br />");
			
			$this->_filters = array();
			
			$this->create_filters();
			
			return $this->_filters;
		}
		
		protected function get_lists() {
			if(!isset($this->_lists)) {
				$this->register_lists();
			}
			return $this->_lists;
		}
		
		protected function get_pages() {
			//echo("\MRouterData\OddCore\PluginBase::get_pages<br />");
			
			if(!isset($this->_pages)) {
				$this->register_pages();
			}
			return $this->_pages;
		}
		
		protected function get_custom_post_types() {
			//echo("\MRouterData\OddCore\PluginBase::custom_post_types<br />");
			
			if(!isset($this->_custom_post_types)) {
				$this->register_custom_post_types();
			}
			return $this->_custom_post_types;
		}
		
		protected function get_filters() {
			//echo("\MRouterData\OddCore\PluginBase::get_filters<br />");
			
			if(!isset($this->_filters)) {
				$this->register_filters();
			}
			return $this->_filters;
		}
		
		protected function register_ajax_api_end_points() {
			//echo("\MRouterData\OddCore\PluginBase::register_ajax_api_end_points<br />");
			
			$this->_ajax_api_end_points = array();
			
			
		}
		
		protected function get_ajax_api_end_points() {
			//echo("\MRouterData\OddCore\PluginBase::get_ajax_api_end_points<br />");
			
			if(!isset($this->_ajax_api_end_points)) {
				$this->register_ajax_api_end_points();
			}
			return $this->_ajax_api_end_points;
		}
		
		protected function get_list_of_ajax_end_points() {
			$returnArray = array();
			
			$ajax_api_end_points = $this->get_ajax_api_end_points();
			foreach($ajax_api_end_points as $current_end_point) {
				$returnArray[] = $current_end_point->get_system_name();
			}
			
			return $returnArray;
		}
		
		protected function create_rest_api_end_point($new_end_point, $path, $namespace, $headers) {
			$new_end_point->add_headers($headers);
			$new_end_point->setup($path, $namespace);
			$this->_rest_api_end_points[] = $new_end_point;
			
			return $new_end_point;
		}
		
		protected function create_rest_api_end_points() {
			//echo("\MRouterData\OddCore\PluginBase::create_rest_api_end_points<br />");
			
			//MENOTE: should be overridden
		}
		
		protected function register_rest_api_end_points() {
			//echo("\MRouterData\OddCore\PluginBase::register_rest_api_end_points<br />");
			
			$this->_rest_api_end_points = array();
			$this->create_rest_api_end_points();
		}
		
		protected function get_rest_api_end_points() {
			//echo("\MRouterData\OddCore\PluginBase::get_rest_api_end_points<br />");
			
			if(!isset($this->_rest_api_end_points)) {
				$this->register_rest_api_end_points();
			}
			return $this->_rest_api_end_points;
		}
		
		protected function add_shortcode($shortcode) {
			//echo("\MRouterData\OddCore\PluginBase::add_shortcode<br />");
			
			$this->_shortcodes[] = $shortcode;
		}
		
		protected function create_shortcodes() {
			//echo("\MRouterData\OddCore\PluginBase::create_shortcodes<br />");
			
			//MENOTE: should be overridden
		}
		
		protected function register_shortcodes() {
			//echo("\MRouterData\OddCore\PluginBase::register_shortcodes<br />");
			
			$this->_shortcodes = array();
			$this->create_shortcodes();
		}
		
		protected function get_shortcodes() {
			//echo("\MRouterData\OddCore\PluginBase::get_shortcodes<br />");
			
			if(!isset($this->_shortcodes)) {
				$this->register_shortcodes();
			}
			return $this->_shortcodes;
		}
		
		protected function add_meta_box($system_name, $box, $post_type = 'post', $context = 'advanced', $priority = 'default') {
			$new_registration = new \MRouterData\OddCore\Admin\MetaData\PostMetaDataBoxRegistration();
			
			$new_registration->setup($system_name, $box, $post_type, $context, $priority);
			
			$this->_meta_boxes[] = $new_registration;
		}
		
		protected function create_meta_boxes() {
			//echo("\MRouterData\OddCore\PluginBase::create_meta_boxes<br />");
			
			//MENOTE: should be overridden
		}
		
		protected function register_meta_boxes() {
			//echo("\MRouterData\OddCore\PluginBase::register_meta_boxes<br />");
			
			$this->_meta_boxes = array();
			$this->create_meta_boxes();
		}
		
		protected function get_meta_boxes() {
			//echo("\MRouterData\OddCore\PluginBase::get_meta_boxes<br />");
			
			if(!isset($this->_meta_boxes)) {
				$this->register_meta_boxes();
			}
			return $this->_meta_boxes;
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
		
		public function register_hooks() {
			//echo("\MRouterData\OddCore\PluginBase::register_hooks<br />");
			
			add_action('init', array($this, 'hook_init'));
			add_action('admin_menu', array($this, 'hook_admin_menu'));
			add_action('admin_enqueue_scripts', array($this, 'hook_admin_enqueue_scripts'));
			add_action('rest_api_init', array($this, 'hook_rest_api_init'));
			add_action('edit_form_after_title', array($this, 'hook_edit_form_after_title'));
			add_action('edit_form_after_editor', array($this, 'hook_edit_form_after_editor'));
			add_action('save_post', array($this, 'hook_save_post'), 10, 3);
			add_action('add_meta_boxes', array($this, 'hook_add_meta_boxes'));
			add_action('wp_enqueue_scripts', array($this, 'hook_wp_enqueue_scripts'));
			
			$ajax_api_end_points = $this->get_ajax_api_end_points();
			foreach($ajax_api_end_points as $current_end_point) {
				$current_end_point->register_hooks();
			}
			
			$shortcodes = $this->get_shortcodes();
			foreach($shortcodes as $shortcode) {
				$shortcode->register();
			}
			
			$meta_boxes = $this->get_meta_boxes();
			foreach($meta_boxes as $meta_box) {
				$meta_box->register_save_hook();
			}
			
			$filters = $this->get_filters();
			foreach($filters as $filter) {
				$filter->register();
			}
			
			$additional_hooks = $this->get_additional_hooks();
			foreach($additional_hooks as $additional_hook) {
				$additional_hook->register();
			}
		}
		
		public function hook_init() {
			
			$custom_post_types = $this->get_custom_post_types();
			foreach($custom_post_types as $custom_post_type) {
				$custom_post_type->register();
			}
		}
		
		public function hook_add_meta_boxes() {
			
			$meta_boxes = $this->get_meta_boxes();
			foreach($meta_boxes as $meta_box) {
				$meta_box->register();
			}
		}
		
		public function hook_admin_menu() {
			//echo("\MRouterData\OddCore\PluginBase::hook_admin_menu<br />");
			
			$pages = $this->get_pages();
			
			foreach($pages as $page) {
				$page->register_page();
			} 
		}
		
		public function hook_admin_enqueue_scripts() {
			//echo("\MRouterData\OddCore\PluginBase::hook_admin_enqueue_scripts<br />");
			
			$screen = get_current_screen();
			$current_page_name = $screen->id;
			
			$pages = $this->get_pages();
			if(isset($pages[$current_page_name])) {
				$current_page = $pages[$current_page_name];
				$current_page->enqueue_scripts_and_styles();
			}
			
			$custom_post_types = $this->get_custom_post_types();
			if(isset($custom_post_types[$current_page_name])) {
				$current_post = $custom_post_types[$current_page_name];
				$current_post->enqueue_scripts_and_styles();
			}
		}
		
		public function hook_rest_api_init() {
			//echo("\MRouterData\OddCore\PluginBase::hook_rest_api_init<br />");
			$api_end_points = $this->get_rest_api_end_points();
			foreach($api_end_points as $current_end_point) {
				$current_end_point->register_hooks();
			}
		}
		
		public function hook_edit_form_after_title() {
			//echo("\MRouterData\OddCore\PluginBase::hook_edit_form_after_title<br />");
			
			$custom_post_types = $this->get_custom_post_types();
			
			$screen = get_current_screen();
			$current_page_name = $screen->id;
			
			if(isset($custom_post_types[$current_page_name])) {
				$current_post = $custom_post_types[$current_page_name];
				$current_post->output_after_title();
			}
		}
		
		public function hook_edit_form_after_editor() {
			//echo("\MRouterData\OddCore\PluginBase::hook_edit_form_after_editor<br />");
			
			$custom_post_types = $this->get_custom_post_types();
			
			$screen = get_current_screen();
			$current_page_name = $screen->id;
			
			if(isset($custom_post_types[$current_page_name])) {
				$current_post = $custom_post_types[$current_page_name];
				$current_post->output_after_editor();
			}
		}
		
		public function hook_save_post($post_id, $post, $update) {
			//echo("\MRouterData\OddCore\PluginBase::hook_save_post<br />");
			
			if(wp_is_post_revision($post_id)) {
				return;
			}
			
			remove_action('save_post', array($this, 'hook_save_post'));
			
			$custom_post_types = $this->get_custom_post_types();
			
			$current_page_name = $post->post_type;
		
			if(isset($custom_post_types[$current_page_name])) {
				$current_post = $custom_post_types[$current_page_name];
				$current_post->verify_and_save($post_id);
			}
		}
		
		public function hook_wp_enqueue_scripts() {
			foreach($this->javascript_files as $id => $path) {
				wp_enqueue_script($id, $path, array(), "wp-".get_bloginfo('version').','.M_ROUTER_DATA_DOMAIN.'-'.M_ROUTER_DATA_VERSION);
			}
		}
		
		public static function test_import() {
			echo("Imported \MRouterData\OddCore\PluginBase<br />");
		}
	}
?>