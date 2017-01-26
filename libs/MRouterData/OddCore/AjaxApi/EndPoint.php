<?php
	namespace MRouterData\OddCore\AjaxApi;
	
	use \WP_Query;
	
	class EndPoint {
		
		protected $_system_name = null;
		protected $_is_public = false;
		
		function __construct() {
			//echo("\OddCore\AjaxApi\EndPoint::__construct<br />");
			
			
		}
		
		public function get_system_name() {
			return $this->_system_name;
		}
		
		public function setup($system_name, $is_public = false) {
			//echo("\OddCore\AjaxApi\EndPoint::setup<br />");
			
			$this->_system_name = $system_name;
			$this->_is_public = $is_public;
			
			return $this;
		}
		
		protected function output_success($data) {
			
			$response_data = array("success" => 1, "data" => $data);
			
			echo(json_encode($response_data));
		}
		
		protected function output_error($message) {
			
			$response_data = array("success" => 0, "message" => $message);
			
			echo(json_encode($response_data));
		}
		
		public function perform_call($data) {
			//echo("\OddCore\AjaxApi\EndPoint::perform_call<br />");
			
			$this->output_error("End point not implemented");
		}
		
		public function hook_perform_call() {
			$this->perform_call($_POST);
			wp_die();
		}
		
		public function register_hooks() {
			add_action("wp_ajax_".$this->_system_name, array($this, "hook_perform_call"));
			
			if($this->_is_public) {
				add_action("wp_ajax_nopriv_".$this->_system_name, array($this, "hook_perform_call"));
			}
		}
		
		public static function test_import() {
			echo("Imported \OddCore\AjaxApi\EndPoint<br />");
		}
	}
?>