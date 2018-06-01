<?php
	namespace Wprr\RestApi;
	
	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\RestApi\PostDataByIdEndPoint
	class PostDataByIdEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\PostDataByIdEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\PostDataByIdEndPoint::perform_call<br />");
			
			$id = $data['id'];
			
			$has_permission_filter_name = M_ROUTER_DATA_DOMAIN.'/id_has_permission';
			
			$has_permission = apply_filters($has_permission_filter_name, true, $id);
			if(!$has_permission) {
				return $this->output_error('Access denied');
			}
			
			do_action(M_ROUTER_DATA_DOMAIN.'/prepare_api_request', $data);
			
			$post = get_post($id);
			
			if(!isset($post)) {
				$this->output_error("Post does not exist");
			}
			
			$return_object = array();
			
			$return_object["url"] = get_permalink($post);
			
			$encoder = new \Wprr\WprrEncoder();
			$return_object["data"] = $encoder->encode_post($post);
			$return_object["performance"] = $encoder->get_performance_data();
			
			return $this->output_success($return_object);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\PostDataByIdEndPoint<br />");
		}
	}
?>