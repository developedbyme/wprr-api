<?php
	namespace Wprr\RestApi\Admin;

	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;

	// \Wprr\RestApi\Admin\ImportItemEndpoint
	class ImportItemEndpoint extends EndPoint {

		function __construct() {
			// echo("\Wprr\RestApi\Admin\ImportItemEndpoint::__construct<br />");
		}

		public function perform_call($data) {
			// echo("\Wprr\RestApi\Admin\ImportItemEndpoint::perform_call<br />");

			//METODO Add security.
			
			$type = $data['type'];
			$id = $data['id'];
			
			$hook_name = 'wprr/import/'.$type;
			
			$has_action = has_action($hook_name);
			
			$return_data = array(
				'logs' => array()
			);
			
			if($has_action) {
				do_action_ref_array($hook_name, array($id, $data, $type, &$return_data));
			}
			else {
				return $this->output_error('Action '.$hook_name.' doesn\'t exist');
			}
			
			return $this->output_success($return_data);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\CreateEditPostEndpoint<br />");
		}
	}
?>