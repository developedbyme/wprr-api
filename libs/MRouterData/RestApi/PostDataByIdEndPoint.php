<?php
	namespace MRouterData\RestApi;
	
	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;
	
	// \MRouterData\RestApi\PostDataByIdEndPoint
	class PostDataByIdEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\PostDataByIdEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\PostDataByIdEndPoint::perform_call<br />");
			
			$id = $data['id'];
			
			$post = get_post($id);
			
			if(!isset($post)) {
				$this->output_error("Post does not exist");
			}
			
			$return_object = array();
			
			$return_object["url"] = get_permalink($post);
			
			$encoder = new \MRouterData\MRouterDataEncoder();
			$return_object["data"] = $encoder->encode_post($post);
			$return_object["performance"] = $encoder->get_performance_data();
			
			return $this->output_success($return_object);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\PostDataByIdEndPoint<br />");
		}
	}
?>