<?php
	namespace Wprr\DataApi\Data\Range;

	// \Wprr\DataApi\Data\Range\SelectRegistration
	class SelectRegistration {
		
		protected $_file = null;
		protected $_class_name = null;
		protected $_selection = null;

		function __construct() {
			
		}
		
		public function setup($file, $class_name) {
			$this->_file = $file;
			$this->_class_name = $class_name;
			
			return $this;
		}
		
		public function load() {
			if(!$this->_selection) {
				require_once($this->_file);
				
				$class = $this->_class_name;
				$this->_selection = new $class();
			}
		}
		
		public function select($query, $data) {
			$this->load();
			
			$this->_selection->select($query, $data);
		}
		
		public function filter($posts, $data) {
			$this->load();
			
			return $this->_selection->filter($posts, $data);
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\SelectRegistration<br />");
		}
	}
?>