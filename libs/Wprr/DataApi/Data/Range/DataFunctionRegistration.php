<?php
	namespace Wprr\DataApi\Data\Range;

	// \Wprr\DataApi\Data\Range\DataFunctionRegistration
	class DataFunctionRegistration {
		
		protected $_file = null;
		protected $_class_name = null;
		protected $_function = null;

		function __construct() {
			
		}
		
		public function setup($file, $class_name) {
			$this->_file = $file;
			$this->_class_name = $class_name;
			
			return $this;
		}
		
		public function load() {
			if(!$this->_function) {
				require_once($this->_file);
				
				$class = $this->_class_name;
				$this->_function = new $class();
			}
		}
		
		public function prepare($ids) {
			$this->load();
			
			if(method_exists($this->_function, 'prepare')) {
				return $this->_function->prepare($ids);
			}
			return null;
		}
		
		public function get_data($data) {
			$this->load();
			
			return $this->_function->get_data($data);
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\DataFunctionRegistration<br />");
		}
	}
?>