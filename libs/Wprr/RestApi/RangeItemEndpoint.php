<?php
	namespace Wprr\RestApi;
	
	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\RestApi\RangeItemEndpoint
	class RangeItemEndpoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\RangeItemEndpoint::__construct<br />");
			
			parent::__construct();
		}
		
		protected function get_filters($names, $type, $data) {
			$return_array = array();
			foreach($names as $name) {
				$has_permission = apply_filters(WPRR_DOMAIN.'/range_'.$type.'_has_permission/'.$name, true, $data);
				if($has_permission) {
					$return_array[] = $name;
				}
				else {
					//METODO: log error
				}
			}
			
			return $return_array;
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\RangeItemEndpoint::perform_call<br />");
			
			try {
				do_action(WPRR_DOMAIN.'/prepare_api_user', $data);
				
				$post_types = $data['post_types'];
				if($post_types !== 'any') {
					$post_types = explode(',', $post_types);
				}
				else {
					$post_types = get_post_types(array(), 'names');
				}
			
				$selections = $this->get_filters(explode(',', $data['selections']), 'selection', $data);
				$encodings = $this->get_filters(explode(',', $data['encodings']), 'encoding', $data);
			
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
			
				$suppress_filters = 0;
				if(isset($data['suppressFilters'])) {
					$suppress_filters = (int)$data['suppressFilters'];
				}
			
				$query_args = array(
					'post_type' => $post_types,
					'posts_per_page' => 1,
					'fields' => 'ids',
					'suppress_filters' => $suppress_filters
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
				foreach($selections as $selection) {
					$filter_name = WPRR_DOMAIN.'/range_filter/'.$selection;
				
					$posts = apply_filters($filter_name, $posts, $data);
				}
			
				$post_links = array();
				foreach($posts as $post_id) {
				
					$encoded_data = array('id' => $post_id);
				
					foreach($encodings as $encoding) {
						$filter_name = WPRR_DOMAIN.'/range_encoding/'.$encoding;
				
						$encoded_data = apply_filters($filter_name, $encoded_data, $post_id, $data);
					}
				
					$post_links[] = $encoded_data;
				};
				
				foreach($encodings as $encoding) {
					$filter_name = WPRR_DOMAIN.'/range_group_encoding/'.$encoding;
					
					$post_links = apply_filters($filter_name, $post_links, $data);
				}
			
				if(count($post_links) === 0) {
					return $this->output_success(null);
				}
			}
			catch(\Exception $exception) {
				return $this->output_error($exception->getMessage());
			}
			
			return $this->output_success($post_links[0]);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\RangeItemEndpoint<br />");
		}
	}
?>