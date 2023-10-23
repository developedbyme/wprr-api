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
			
			if(isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN']) {
				header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
				header('Access-Control-Allow-Credentials: true');
			}
			else {
				header('Access-Control-Allow-Origin: *');
			}
			
			header('Vary: Origin, Cookie');
			header('Content-Type: application/json; charset=utf-8');
			header('Cache-Control: no-cache, no-store, must-revalidate');
			header('Pragma: no-cache');
			header('Expires: 0');
			
			echo($encoded_reponse);
			
			$this->_has_output = true;
			die();
		}
		
		public function output_response($data) {
			global $wprr_data_api;
			
			$json_error = json_last_error();
			if($json_error) {
				$this->output_api_error('Could not encode JSON (error: '.$json_error.')');
			}
			
			if(isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN']) {
				header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
				header('Access-Control-Allow-Credentials: true');
			}
			else {
				header('Access-Control-Allow-Origin: *');
			}
			
			header('Vary: Origin, Cookie');
			header('Cache-Control: no-cache, no-store, must-revalidate');
			header('Pragma: no-cache');
			header('Expires: 0');
			
			echo($data);
			
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
			
			if(isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN']) {
				header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
				header('Access-Control-Allow-Credentials: true');
			}
			else {
				header('Access-Control-Allow-Origin: *');
			}
			
			header('Vary: Origin, Cookie');
			header('Content-Type: application/json; charset=utf-8');
			header('Cache-Control: no-cache, no-store, must-revalidate');
			header('Pragma: no-cache');
			header('Expires: 0');
		
			echo(json_encode($reposonse));
			
			$this->_has_output = true;
			die();
		}
		
		public function output_error($message) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
			
			if(isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN']) {
				header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
				header('Access-Control-Allow-Credentials: true');
			}
			else {
				header('Access-Control-Allow-Origin: *');
			}
			
			header('Vary: Origin, Cookie');
			header('Cache-Control: no-cache, no-store, must-revalidate');
			header('Pragma: no-cache');
			header('Expires: 0');
		
			echo($message);
			
			if(!empty($this->_logs)) {
				echo("\n\n");
				echo(json_encode($this->_logs));
			}
			
			$this->_has_output = true;
			die();
		}
		
		public function redirect($url) {
			header('Location: ' . $url, true, 302);
			die();
		}
		
		public function redirect_to_action_complete() {
			
			global $wprr_data_api;
			
			$redirect_url = null;
			
			$subquery = $wprr_data_api->range()->new_query()->include_private()->include_term_by_path('dbm_type', 'global-item')->meta_query('identifier', 'page/actionComplete'); 
			
			$from_ids = $subquery->get_ids();
			$from_posts = \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationQuery::get_posts($wprr_data_api->wordpress()->get_posts($from_ids), 'out:pointing-to:*');
			
			if(!empty($from_posts)) {
				$redirect_url = SITE_URL.'/'.$from_posts[0]->get_link();
			}
			
			if(!$redirect_url) {
				$redirect_url = SITE_URL.'/'.'action-completed';
			}
			
			$this->redirect($redirect_url);
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Output<br />");
		}
	}
?>