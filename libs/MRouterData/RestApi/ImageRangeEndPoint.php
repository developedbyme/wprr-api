<?php
	namespace MRouterData\RestApi;
	
	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;
	
	// \MRouterData\RestApi\ImageRangeEndPoint
	class ImageRangeEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\ImageRangeEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\ImageRangeEndPoint::perform_call<br />");
			
			$post_type = $data['post_type'];
			
			$general_has_permission_filter_name = M_ROUTER_DATA_DOMAIN.'/range_has_permission';
			$specific_has_permission_filter_name = M_ROUTER_DATA_DOMAIN.'/range_has_permission_'.$post_type;
			
			$has_permission = apply_filters($general_has_permission_filter_name, true, $data);
			$has_permission = apply_filters($specific_has_permission_filter_name, $has_permission, $data);	
			if(!$has_permission) {
				return $this->output_error('Access denied');
			}
			
			do_action(M_ROUTER_DATA_DOMAIN.'/prepare_api_request', $data);
			
			$query_args = array(
				'post_type' => $post_type,
				'posts_per_page' => -1,
				'fields' => 'ids'
			);
			if(isset($data['taxonomy'])) {
				
				if(isset($data['terms'])) {
					$terms = explode(',', $data['terms']);
				}
				else {
					$terms = array($data['term']);
				}
				
				$tax_query = array(
					'taxonomy' => $data['taxonomy'],
					'field' => 'slug',
					'terms' => $terms
				);
				
				if(isset($data['includeTermChildren'])) {
					$tax_query['include_children'] = ($data['includeTermChildren'] == '1') ? true : false;
				}
				
				$query_args['tax_query'] = array();
				$query_args['tax_query'][] = $tax_query;
			}
			
			$posts = get_posts($query_args);
			
			$post_links = array();
			$encoder = new \MRouterData\MRouterDataEncoder();
			
			foreach($posts as $post_id) {
				$post_links[] = $encoder->encode_image(get_post($post_id));
			};
			
			return $this->output_success($post_links);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\ImageRangeEndPoint<br />");
		}
	}
?>