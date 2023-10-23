<?php
	namespace Wprr\DataApi;

	// \Wprr\DataApi\Registry
	class Registry {
		
		protected $_items = array();
		
		function __construct() {
			
		}
		
		public function get_value($id) {
			return $this->_items[$id];
		}
		
		public function get_array($id) {
			if(!isset($this->_items[$id])) {
				$this->_items[$id] = array();
			}
			
			return $this->_items[$id];
		}
		
		public function add_to_array($id, $data) {
			if(!isset($this->_items[$id])) {
				$this->_items[$id] = array();
			}
			
			$this->_items[$id][] = $data;
			
			return $this;
		}
		
		public function set_value($id, $value) {
			$this->_items[$id] = $value;
			
			return $this;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Registry<br />");
		}
	}
?>