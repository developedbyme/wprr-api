<?php
	namespace Wprr\DataApi\Data\Range;

	// \Wprr\DataApi\Data\Range\EncodingRegistration
	class EncodingRegistration {
		
		protected $_file = null;
		protected $_class_name = null;
		protected $_encoder = null;

		function __construct() {
			
		}
		
		public function setup($file, $class_name) {
			$this->_file = $file;
			$this->_class_name = $class_name;
			
			return $this;
		}
		
		public function load() {
			if(!$this->_encoder) {
				require_once($this->_file);
				
				$class = $this->_class_name;
				$this->_encoder = new $class();
			}
		}
		
		public function prepare($ids) {
			$this->load();
			
			if(method_exists($this->_encoder, 'prepare')) {
				return $this->_encoder->prepare($ids);
			}
			return null;
		}
		
		public function encode($id) {
			$this->load();
			
			return $this->_encoder->encode($id);
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\EncodingRegistration<br />");
		}
	}
?>