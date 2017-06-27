<?php
	//use \WP_REST_Request;
	
	function get_initial_mrouter_data() {
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

		$initial_mrouter_data = array();
		$initial_mrouter_data['data'] = array();
		
		$current_data = mrouter_encode();
		$initial_mrouter_data['data'][$protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']] = array(
			'status' => 1,
			'data' => $current_data['data']
		);
		
		
		$api_calls = null;
		
		if(is_singular()) {
			$api_calls = get_post_meta(get_the_id(), 'mrouter_initital_load', true);
		}
		
		if($api_calls) {
			
			$initial_mrouter_data['apiData'] = array();
			
			$rest_server = rest_get_server();
			
			foreach($api_calls as $api_call) {
				$current_local_path = $api_call;
				$current_url = get_rest_url(null, '/'.$current_local_path);
				$api_request = \WP_REST_Request::from_url($current_url);
		
				$api_response = $rest_server->dispatch($api_request);
		
				//METODO: check for ok response
				$initial_mrouter_data['apiData'][$current_local_path] = array(
					'status' => 1,
					'data' => $api_response->data['data']
				);
			}
		}
		
		
		return $initial_mrouter_data;
	}
	
	function mrouter_output_rendered_content() {
		$permalink = get_permalink();
		
		$upload_dir = wp_upload_dir(null, false);
		
		$salt = apply_filters('m_router_data/salt', 'wvIUIAULTxKicDpbkzyPpVi5wskSe6Yxy0Uq4wCqbAui1wVKAKmsVhN7JOhGbFQohVs9pnpQoS1dWGkL');
		
		$rendered_path = $upload_dir['basedir'].'/mrouter-seo-renders/'.md5($permalink.$salt).'.html';
		
		if(file_exists($rendered_path)) {
			readfile($rendered_path);
		}
	}
	
	function mrouter_encode() {
		$encoder = new \MRouterData\MRouterDataEncoder();
		
		return $encoder->encode();
	}
	
	function mrouter_encode_post_link($post_id) {
		$encoder = new \MRouterData\MRouterDataEncoder();
		
		return $encoder->encode_post_link($post_id);
	}
	
	function mrouter_encode_term($term) {
		$encoder = new \MRouterData\MRouterDataEncoder();
		
		return $encoder->encode_term($term);
	}
	
	function mrouter_disable_all_ranges($filter_priority = 10) {
		add_filter('m_router_data/range_has_permission', function($has_permission) {return false;}, $filter_priority, 1);
	}
	
	global $mrouter_disabled_post_types;
	$mrouter_disabled_post_types = array();
	
	function mrouter_disable_data_for_post_type($post_type, $filter_priority = 10) {
		
		global $mrouter_disabled_post_types;
		$mrouter_disabled_post_types[] = $post_type;
		
		add_filter('m_router_data/range_has_permission_'.$post_type, function($has_permission) {return false;}, $filter_priority, 1);
		add_filter('m_router_data/id_has_permission', function($has_permission, $id) {
			if(!$has_permission) {
				return $has_permission;
			}
			
			global $mrouter_disabled_post_types;
			$post_type = get_post_type($id);
			
			if(in_array($post_type, $mrouter_disabled_post_types)) {
				return false;
			}
			return true;
		}, $filter_priority, 2);
	}
?>