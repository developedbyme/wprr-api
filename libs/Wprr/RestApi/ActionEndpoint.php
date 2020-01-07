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
			
			$type = $data['action_name'];
			
			$hook_name = 'wprr/api_action/'.$type;
			
			$has_action = has_action($hook_name);
			
			$return_data = array(
				'logs' => array()
			);
			
			if($has_action) {
				try {
					do_action_ref_array($hook_name, array($data, &$return_data));
				}
				catch(\Exception $error) {
					return $this->output_error($error->getMessage());
				}
			}
			else {
				return $this->output_error('Action '.$hook_name.' doesn\'t exist');
			}
			
			return $this->output_success($return_data);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\ActionEndpoint<br />");
		}
	}
