<?php
	namespace MRouterData\RestApi;
	
	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;
	
	// \MRouterData\RestApi\AcfOptionsEndPoint
	class AcfOptionsEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\AcfOptionsEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\AcfOptionsEndPoint::perform_call<br />");
			
			
			do_action(M_ROUTER_DATA_DOMAIN.'/prepare_api_request', $data);
			
			$fields_object = get_field_objects('option', false, true);
			
			$encoder = new \MRouterData\MRouterDataEncoder();
			
			$data = $encoder->encode_acf_options();
			
			return $this->output_success($data);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\AcfOptionsEndPoint<br />");
		}
	}
?>