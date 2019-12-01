<?php
	namespace Wprr\RestApi;
	
	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\RestApi\RangeEndpoint
	class RangeEndpoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\RangeEndpoint::__construct<br />");
			
			
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
			//echo("\OddCore\RestApi\RangeEndpoint::perform_call<br />");
			
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
			
			$number_of_items = -1;
			if(isset($data['maxNumberOfPosts'])) {
				$number_of_items = (int)$data['maxNumberOfPosts'];
			}
			
			$query_args = array(
				'post_type' => $post_types,
				'posts_per_page' => $number_of_items,
				'fields' => 'ids',
				'suppress_filters' => 0
			);
			
			if(isset($data['startFrom'])) {
				$query_args['offset'] = (int)$data['startFrom'];
			}
			
			if(isset($data['order'])) {
				$query_args['order'] = $data['order'];
			}
			if(isset($data['orderby'])) {
				$query_args['orderby'] = $data['orderby'];
			}
			
			try {
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
			}
			catch(\Exception $exception) {
				return $this->output_error($exception->getMessage());
			}
			
			return $this->output_success($post_links);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\RangeEndpoint<br />");
		}
	}
?>