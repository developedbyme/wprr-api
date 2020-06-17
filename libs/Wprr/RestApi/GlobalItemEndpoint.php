<?php
	namespace Wprr\RestApi;
	
	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\RestApi\GlobalItemEndpoint
	class GlobalItemEndpoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\GlobalItemEndpoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\GlobalItemEndpoint::perform_call<br />");
			
			$filter_name = WPRR_DOMAIN.'/global-item/'.$data['item'];
			
			if(!has_filter($filter_name)) {
				return $this->output_error('No global item '.$data['item']);
			}
			
			$return_array = array();
			
			do_action(WPRR_DOMAIN.'/prepare_api_request', $data);
			
			try {
				$return_object = apply_filters($filter_name, array(), $data['item'], $data);
			}
			catch(\Exception $exception) {
				return $this->output_error($exception->getMessage());
			}
			
			return $this->output_success($return_object);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\GlobalItemEndpoint<br />");
		}
	}
?>