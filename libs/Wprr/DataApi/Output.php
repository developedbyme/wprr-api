<?php
	namespace Wprr\DataApi;

	// \Wprr\DataApi\Output
	class Output {
		
		protected $_has_output = false;
		
		function __construct() {
			
		}
		
		public function has_output() {
			return $this->_has_output;
		}
		
		public function output_api_repsponse($data) {
			$reposonse = array(
				'code' => 'success',
				'data' => $data,
				'performance' => array()
			);

			header('Content-Type: application/json; charset=utf-8');
			header('Cache-Control: no-cache, no-store, must-revalidate');
			header('Pragma: no-cache');
			header('Expires: 0');
		
			echo(json_encode($reposonse));
			
			$this->_has_output = true;
			die();
		}
		
		public function output_api_error($message, $error = null) {
			$reposonse = array(
				'code' => 'error',
				'data' => null,
				'message' => $message,
				'error' => $error,
				'performance' => array()
			);
			
			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
			header('Content-Type: application/json; charset=utf-8');
			header('Cache-Control: no-cache, no-store, must-revalidate');
			header('Pragma: no-cache');
			header('Expires: 0');
		
			echo(json_encode($reposonse));
			
			$this->_has_output = true;
			die();
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Output<br />");
		}
	}
?>