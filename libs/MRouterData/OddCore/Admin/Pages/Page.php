<?php
	namespace MRouterData\OddCore\Admin\Pages;
	
	class Page {
		
		//METODO: create base object for posts and pages
		
		protected $_system_name = null;
		protected $_name = null;
		protected $_menu_name = null;
		
		public $javascript_files = array();
		public $javascript_data = array();
		public $css_files = array();
		
		function __construct() {
			//echo("\OddCore\Admin\Pages\Page::__construct<br />");
			
			
		}
		
		public function get_system_name() {
			return $this->_system_name;
		}
		
		public function set_names($name, $menu_name, $system_name) {
			//echo("\OddCore\Admin\Pages\Page::set_names<br />");
			
			$this->_name = $name;
			$this->_menu_name = $menu_name;
			$this->_system_name = $system_name;
			
			return $this;
		}
		
		public function register_page() {
			//echo("\OddCore\Admin\Pages\Page::register_page<br />");
			
			add_menu_page( 
				$this->_name, 
				$this->_menu_name, 
				'manage_options', 
				$this->_system_name, 
				array($this, "output") 
			);
		}
		
		public function enqueue_scripts_and_styles() {
			//echo("\OddCore\Admin\Pages\Page::enqueue_scripts_and_styles<br />");
			
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
			//echo("\OddCore\Admin\Pages\Page::add_javascript_data<br />");
			
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
		
		public function output() {
			//echo("\OddCore\Admin\Pages\Page::output<br />");
			
			//MENOTE: should be overridden
			?>
				<h1>Page (<?php echo($this->_name); ?>) has not been implemented</h1>
			<?php
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\Pages\Page<br />");
		}
	}
?>