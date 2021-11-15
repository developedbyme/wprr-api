<?php
	namespace Wprr\DataApi;

	// \Wprr\DataApi\Output
	class Output {
		
		protected $_has_output = false;
		protected $_logs = array();
		
		function __construct() {
			
		}
		
		public function has_output() {
			return $this->_has_output;
		}
		
		public function log($message) {
			$this->_logs[] = (string)$message;
		}
		
		public function output_api_repsponse($data) {
			//var_dump('output_api_repsponse');
			//var_dump($data);
			
			global $wprr_data_api;
			
			$reposonse = array(
				'code' => 'success',
				'data' => $data,
				'performance' => $wprr_data_api->performance()->get_stats(),
				'logs' => $this->_logs
			);
			
			$encoded_reponse = json_encode($reposonse);
			$json_error = json_last_error();
			if($json_error) {
				$this->output_api_error('Could not encode JSON (error: '.$json_error.')');
			}

			header('Content-Type: application/json; charset=utf-8');
			header('Cache-Control: no-cache, no-store, must-revalidate');
			header('Pragma: no-cache');
			header('Expires: 0');
		
			echo($encoded_reponse);
			
			$this->_has_output = true;
			die();
		}
		
		public function output_api_error($message, $error = null) {
			
			$reposonse = array(
				'code' => 'error',
				'data' => null,
				'message' => $message,
				'error' => $error,
				'performance' => null,
				'logs' => $this->_logs
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