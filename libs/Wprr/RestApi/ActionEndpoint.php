<?php
	namespace Wprr\RestApi;

	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;

	// \Wprr\RestApi\ActionEndpoint
	class ActionEndpoint extends EndPoint {

		function __construct() {
			// echo("\OddCore\RestApi\ActionEndpoint::__construct<br />");
		}

		public function perform_call($data) {
			// echo("\OddCore\RestApi\ActionEndpoint::perform_call<br />");
			
			$type = $data['type'];
			
			$hook_name = 'wprr/api_action/'.$type;
			
			$has_action = has_action('wprr/api_action/'.$type);
			
			if($has_action) {
				do_action($hook_name, $data);
			}
			else {
				return $this->output_error('Action '.$hook_name.' doesn\'t exist');
			}
			
			return $this->output_success($has_action);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\ActionEndpoint<br />");
		}
	}
