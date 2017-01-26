<?php
	namespace MRouterData\OddCore\RestApi;
	
	use \WP_Query;
	use MRouterData\OddCore\RestApi\EndPoint as EndPoint;
	
	// \MRouterData\OddCore\RestApi\SaveFileEndPoint
	class SaveFileEndPoint extends EndPoint {
		
		protected $_base_path = null;
		
		function __construct() {
			//echo("\OddCore\RestApi\SaveFileEndPoint::__construct<br />");
			
			
		}
		
		public function set_base_path($path) {
			
			$this->_base_path = $path;
			
			return $this;
		}
		
		protected function create_folders_and_save_file($full_path, $content) {
			
			$parts = explode('/', $full_path);
			$file = array_pop($parts);
			$dir = '';
			
			foreach($parts as $part) {
				if(!is_dir($dir .= "/$part")) {
					mkdir($dir);
				}
			}
			
			return file_put_contents($full_path, $content);
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\SaveFileEndPoint::perform_call<br />");
			
			$path = $data['path'];
			$content = $data['content'];
			$encoding = $data['encoding'];
			
			$full_path = $this->_base_path."/".$path;
			
			if(isset($encoding)) {
				if($encoding === "base64") {
					$content = base64_decode($content);
				}
				else {
					return $this->output_error("Unknown encoding (".$encoding.").");
				}
			}
			
			//MENOTE: stripslashes needs to be changed to save binary files
			$bytes_written = $this->create_folders_and_save_file($full_path, stripslashes($content));
			
			if($bytes_written === false) {
				return $this->output_error("Couldn't write.");
			}
			
			return $this->output_success(null);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\SaveFileEndPoint<br />");
		}
	}
?>