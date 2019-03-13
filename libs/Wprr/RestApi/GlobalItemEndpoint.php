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
			
			$return_array = array();
			
			do_action(M_ROUTER_DATA_DOMAIN.'/prepare_api_request', $data);
			
			$filter_name = WPRR_DOMAIN.'/global-item/'.$data['item'];
			
			if(!has_filter($filter_name)) {
				return $this->output_error('No global item '.$data['item']);
			}
			
			$return_object = apply_filters($filter_name, array(), $data['item'], $data);
			
			return $this->output_success($return_object);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\GlobalItemEndpoint<br />");
		}
	}
?>