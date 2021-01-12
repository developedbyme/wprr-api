<?php
	namespace Wprr\RestApi;
	
	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\RestApi\CommentsEndPoint
	class CommentsEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\CommentsEndPoint::__construct<br />");
			
			parent::__construct();
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\CommentsEndPoint::perform_call<br />");
			
			$id = $data['id'];
			
			$has_permission_filter_name = M_ROUTER_DATA_DOMAIN.'/id_has_permission';
			
			$has_permission = apply_filters($has_permission_filter_name, true, $id);
			if(!$has_permission) {
				return $this->output_error('Access denied');
			}
			
			do_action(M_ROUTER_DATA_DOMAIN.'/prepare_api_request', $data);
			
			$comments_arguments = array(
				'post_id' => $id,
				'parent' => 0
			);
			$comments = get_comments($comments_arguments);
			//var_dump($comments);
			
			$return_array = array();
			
			$encoder = new \Wprr\WprrEncoder();
			
			foreach($comments as $comment) {
				$return_array[] = $encoder->encode_comment($comment);
			}
			
			$return_object = array();
			
			
			$return_object["data"] = $return_array;
			$return_object["performance"] = $encoder->get_performance_data();
			
			return $this->output_success($return_object);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\CommentsEndPoint<br />");
		}
	}
?>