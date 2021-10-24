<?php
	namespace Wprr\DataApi;

	// \Wprr\DataApi\DataApiController
	class DataApiController {

		protected $_database = null;

		function __construct() {
			
		}
		
		public function database() {
			if(!$this->_database) {
				$this->_database = new \Wprr\DataApi\DataBase();
			}
			
			return $this->_database;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\DataApiController<br />");
		}
	}
?>
