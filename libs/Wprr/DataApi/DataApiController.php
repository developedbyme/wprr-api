<?php
	namespace Wprr\DataApi;

	// \Wprr\DataApi\DataApiController
	class DataApiController {

		protected $_database = null;
		protected $_files = null;
		protected $_user = null;
		protected $_output = null;
		protected $_range = null;
		protected $_wordpress = null;
		protected $_performance = null;
		protected $_action = null;
		protected $_http_request = null;
		protected $_registry = null;
		protected $_auto_loader = null;

		function __construct() {
			
		}
		
		public function database() {
			if(!$this->_database) {
				$this->_database = new \Wprr\DataApi\Database();
			}
			
			return $this->_database;
		}
		
		public function files() {
			if(!$this->_files) {
				$this->_files = new \Wprr\DataApi\Files();
			}
			
			return $this->_files;
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
		
		public function action() {
			if(!$this->_action) {
				$this->_action = new \Wprr\DataApi\Data\Action\ActionController();
			}
			
			return $this->_action;
		}
		
		public function registry() {
			if(!$this->_registry) {
				$this->_registry = new \Wprr\DataApi\Registry();
			}
			
			return $this->_registry;
		}
		
		public function http_request() {
			if(!$this->_http_request) {
				$this->_http_request = new \Wprr\DataApi\HttpRequest();
			}
			
			return $this->_http_request;
		}
		
		public function auto_loader() {
			if(!$this->_auto_loader) {
				$this->_auto_loader = new \Wprr\DataApi\System\AutoLoaderController();
			}
			
			return $this->_auto_loader;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\DataApiController<br />");
		}
	}
?>
