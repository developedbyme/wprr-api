<?php
	namespace MRouterData\RestApi;
	
	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;
	
	// \MRouterData\RestApi\RangeEndpoint
	class RangeEndpoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\RangeEndpoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\RangeEndpoint::perform_call<br />");
			
			$post_types = $data['post_types'];
			if($post_types !== 'any') {
				$post_types = explode(',', $post_types);
			}
			
			$selections = explode(',', $data['selections']);
			$encodings = explode(',', $data['encodings']);
			
			$general_has_permission_filter_name = WPRR_DOMAIN.'/range_has_permission';
			
			$has_permission = apply_filters($general_has_permission_filter_name, true, $data);
			foreach($post_types as $post_type) {
				$specific_has_permission_filter_name = WPRR_DOMAIN.'/range_has_permission_'.$post_type;
				$has_permission = apply_filters($specific_has_permission_filter_name, $has_permission, $data);	
			}
			
			if(!$has_permission) {
				return $this->output_error('Access denied');
			}
			
			do_action(WPRR_DOMAIN.'/prepare_api_request', $data);
			
			$query_args = array(
				'post_type' => $post_types,
				'posts_per_page' => -1,
				'fields' => 'ids'
			);
			
			if(isset($data['order'])) {
				$query_args['order'] = $data['order'];
			}
			if(isset($data['orderby'])) {
				$query_args['orderby'] = $data['orderby'];
			}
			
			foreach($selections as $selection) {
				$filter_name = WPRR_DOMAIN.'/range_query/'.$selection;
				
				$query_args = apply_filters($filter_name, $query_args, $data);
			}
			
			$posts = get_posts($query_args);
			
			$post_links = array();
			
			foreach($posts as $post_id) {
				
				$encoded_data = array('id' => $post_id);
				
				foreach($encodings as $encoding) {
					$filter_name = WPRR_DOMAIN.'/range_encoding/'.$encoding;
				
					$encoded_data = apply_filters($filter_name, $encoded_data, $post_id, $data);
				}
				
				$post_links[] = $encoded_data;
			};
			
			return $this->output_success($post_links);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\RangeEndpoint<br />");
		}
	}
?>