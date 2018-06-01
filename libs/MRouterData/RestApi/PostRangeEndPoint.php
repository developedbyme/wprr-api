<?php
	namespace Wprr\RestApi;
	
	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\RestApi\PostRangeEndPoint
	class PostRangeEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\PostRangeEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\PostRangeEndPoint::perform_call<br />");
			
			$post_type = $data['post_type'];
			
			$general_has_permission_filter_name = M_ROUTER_DATA_DOMAIN.'/range_has_permission';
			$specific_has_permission_filter_name = M_ROUTER_DATA_DOMAIN.'/range_has_permission_'.$post_type; //METODO: solve for multiple post types
			
			$has_permission = apply_filters($general_has_permission_filter_name, true, $data);
			$has_permission = apply_filters($specific_has_permission_filter_name, $has_permission, $data);	
			if(!$has_permission) {
				return $this->output_error('Access denied');
			}
			
			do_action(M_ROUTER_DATA_DOMAIN.'/prepare_api_request', $data);
			
			if($post_type !== 'any') {
				$post_type = explode(',', $post_type);
			}
			
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
				
				$query_args['tax_query'] = array();
				$query_args['tax_query'][] = $tax_query;
			}
			
			if(isset($data['order'])) {
				$query_args['order'] = $data['order'];
			}
			if(isset($data['orderby'])) {
				$query_args['orderby'] = $data['orderby'];
			}
			
			$posts = get_posts($query_args);
			
			$post_links = array();
			$encoder = new \Wprr\WprrEncoder();
			
			foreach($posts as $post_id) {
				$post_links[] = $encoder->encode_post_link($post_id);
			};
			
			return $this->output_success($post_links);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\PostRangeEndPoint<br />");
		}
	}
?>