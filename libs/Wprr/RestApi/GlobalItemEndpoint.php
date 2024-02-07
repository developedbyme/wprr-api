<?php
	namespace Wprr\RestApi;
	
	use \Wprr\Core\RestApi\EndPoint as EndPoint;
	
	// \Wprr\RestApi\GlobalItemEndpoint
	class GlobalItemEndpoint extends EndPoint {
		
		function __construct() {
			//echo("\Core\RestApi\GlobalItemEndpoint::__construct<br />");
			
			parent::__construct();
		}
		
		public function perform_call($data) {
			//echo("\Core\RestApi\GlobalItemEndpoint::perform_call<br />");
			
			try {
				$filter_name = WPRR_DOMAIN.'/global-item/'.$data['item'];
			
				if(!has_filter($filter_name)) {
					return $this->output_error('No global item '.$data['item']);
				}
			
				$return_array = array();
			
				do_action(WPRR_DOMAIN.'/prepare_api_request', $data);
				
				$return_object = apply_filters($filter_name, array(), $data['item'], $data);
			}
			catch(\Exception $exception) {
				return $this->output_error($exception->getMessage());
			}
			
			return $this->output_success($return_object);
		}
		
		public static function test_import() {
			echo("Imported \Core\RestApi\GlobalItemEndpoint<br />");
		}
	}
?>