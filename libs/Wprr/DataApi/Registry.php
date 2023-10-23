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
		
		public function apply_filters($id, $result, ...$data) {
			$filters = $this->get_array($id);
			
			foreach($filters as $callable) {
				
				if(!is_callable($callable)) {
					throw(new \Exception('Can\'t call '.$callable));
				}
				
				$result = call_user_func_array($callable, array_merge(array($result), $data));
			}
			
			return $result;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Registry<br />");
		}
	}
?>