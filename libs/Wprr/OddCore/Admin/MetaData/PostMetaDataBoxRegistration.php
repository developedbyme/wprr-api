<?php
	namespace Wprr\OddCore\Admin\MetaData;
	
	// \Wprr\OddCore\Admin\MetaData\PostMetaDataBoxRegistration
	class PostMetaDataBoxRegistration {
		
		protected $_system_name = null;
		protected $_box = null;
		protected $_post_type = 'post';
		protected $_context = 'advanced';
		protected $_priority = 'default';
		
		function __construct() {
			//echo("\OddCore\Admin\MetaData\PostMetaDataBoxRegistration::__construct<br />");
			
			
		}
		
		public function setup($system_name, $box, $post_type = 'post', $context = 'advanced', $priority = 'default') {
			
			$this->_system_name = $system_name;
			$this->_box = $box;
			$this->_post_type = $post_type;
			$this->_context = $context;
			$this->_priority = $priority;
			
			return $this;
		}
		
		public function save($post_id) {
			//echo("\OddCore\Admin\MetaData\PostMetaDataBoxRegistration::save<br />");
			
			remove_action('save_post', array($this, 'save'));
			
			$this->_box->verify_and_save($post_id);
		}
		
		public function register() {
			//echo("\OddCore\Admin\MetaData\PostMetaDataBoxRegistration::register<br />");
			
			add_meta_box( 
				$this->_system_name, 
				__($this->_box->get_name(), WPRR_TEXTDOMAIN), 
				array($this->_box, 'output_registered_box'), 
				$this->_post_type, 
				$this->_context, 
				$this->_priority
			);
		}
		
		public function register_save_hook() {
			//echo("\OddCore\Admin\MetaData\PostMetaDataBoxRegistration::register_save_hook<br />");
			
			add_action('save_post', array($this, 'save'));
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\MetaData\PostMetaDataBoxRegistration<br />");
		}
	}
?>