<?php
	namespace Wprr\RestApi;

	use \Wprr\Core\RestApi\EndPoint as EndPoint;

	// \Wprr\RestApi\ActionEndpoint
	class ActionEndpoint extends EndPoint {

		function __construct() {
			// echo("\Core\RestApi\ActionEndpoint::__construct<br />");
			
			parent::__construct();
		}

		public function perform_call($data) {
			// echo("\Core\RestApi\ActionEndpoint::perform_call<br />");
			
			do_action(WPRR_DOMAIN.'/prepare_api_user', $data);
			
			$type = $data['action_name'];
			
			$hook_name = 'wprr/api_action/'.$type;
			
			$has_action = has_action($hook_name);
			
			$return_data = array(
				'logs' => array()
			);
			
			if($has_action) {
				do_action(WPRR_DOMAIN.'/prepare_api_request', $data);
				
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
			echo("Imported \Core\RestApi\ActionEndpoint<br />");
		}
	}
