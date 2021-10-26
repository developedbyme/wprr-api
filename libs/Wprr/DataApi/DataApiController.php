<?php
	namespace Wprr\DataApi;

	// \Wprr\DataApi\DataApiController
	class DataApiController {

		protected $_database = null;
		protected $_user = null;
		protected $_output = null;

		function __construct() {
			
		}
		
		public function database() {
			if(!$this->_database) {
				$this->_database = new \Wprr\DataApi\DataBase();
			}
			
			return $this->_database;
		}
		
		public function user() {
			if(!$this->_user) {
				$this->_user = new \Wprr\DataApi\User();
			}
			
			return $this->_user;
		}
		
		public function output() {
			if(!$this->_output) {
				$this->_output = new \Wprr\DataApi\Output();
			}
			
			return $this->_output;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\DataApiController<br />");
		}
	}
?>
