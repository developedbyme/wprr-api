<?php
	namespace Wprr\DataApi\System;

	// \Wprr\DataApi\System\AutoLoader
	class AutoLoader {
		
		protected $_namespace;
		protected $_directory;

		function __construct() {
			
		}
		
		public function register($namespace, $directory) {
			
			$this->_namespace = $namespace;
			$this->_directory = $directory;
			
			spl_autoload_register(array($this, 'load_class'));
			
			return $this;
		}
		
		public function load_class( $class ) {
			//var_dump("load_class");
			//var_dump($class);
	
			$namespace_length = strlen($this->_namespace);
			
			if ( substr( $class, 0, $namespace_length ) != $this->_namespace ) {
				return false;
			}
			
			if ( substr( $class, 0, $namespace_length+1 ) == $this->_namespace."\\" ) {
				
				$path = explode( "\\", $class );
				unset( $path[0] );
				
				$class_file = $this->_directory . '/' . implode( "/", $path ) . ".php";
				
				if(is_file($class_file)) {
					require($class_file);
					if(!class_exists($class)) {
						throw(new \Exception('Class '.$class.' is not found in file '.$class_file));
					}
					return true;
				}
				else {
					throw(new \Exception('No file '.$class_file));
				}
			}
			
			return false;

		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\System\AutoLoader<br />");
		}
	}
?>
