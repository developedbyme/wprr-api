<?php
	namespace Wprr\DataApi;

	// \Wprr\DataApi\DataApiController
	class DataApiController {

		protected $_database = null;
		protected $_user = null;
		protected $_output = null;
		protected $_range = null;
		protected $_wordpress = null;
		protected $_performance = null;

		function __construct() {
			
		}
		
		public function database() {
			if(!$this->_database) {
				$this->_database = new \Wprr\DataApi\Database();
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
		
		public function range() {
			if(!$this->_range) {
				$this->_range = new \Wprr\DataApi\Data\Range\RangeController();
			}
			
			return $this->_range;
		}
		
		public function wordpress() {
			if(!$this->_wordpress) {
				$this->_wordpress = new \Wprr\DataApi\WordPress\WordPress();
			}
			
			return $this->_wordpress;
		}
		
		public function performance() {
			if(!$this->_performance) {
				$this->_performance = new \Wprr\DataApi\Performance();
			}
			
			return $this->_performance;
		}
		
		public function http_request() {
			if(!$this->_http_requeset) {
				$this->_http_requeset = new \Wprr\DataApi\HttpRequest();
			}
			
			return $this->_http_requeset;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\DataApiController<br />");
		}
	}
?>
