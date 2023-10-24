<?php
	namespace Wprr\DataApi\System;

	// \Wprr\DataApi\System\AutoLoaderController
	class AutoLoaderController {

		protected $_loaders = array();

		function __construct() {
			
		}
		
		public function add_auto_loader($namespace, $directory) {
			
			$loader = new \Wprr\DataApi\System\AutoLoader();
			$loader->register($namespace, $directory);
			$this->_loaders[] = $loader;
			
			return $this;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\System\AutoLoaderController<br />");
		}
	}
?>
