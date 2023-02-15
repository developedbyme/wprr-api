<?php
	namespace Wprr;

	class Logger {
		
		protected $_logs = array();
		protected $_return_data = array();

		function __construct() {
			
		}
		
		public function add_log($log_text) {
			$this->_logs[] = $log_text;
		}
		
		public function add_return_data($field, $data) {
			$this->_return_data[$field] = $data;
			
			return $this;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\Logger<br />");
		}
	}
?>
