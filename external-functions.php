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
	
	function mrouter_output_rendered_content($path) {
		
		$upload_dir = wp_upload_dir(null, false);
		
		$salt = apply_filters('m_router_data/salt', 'wvIUIAULTxKicDpbkzyPpVi5wskSe6Yxy0Uq4wCqbAui1wVKAKmsVhN7JOhGbFQohVs9pnpQoS1dWGkL');
		$render_key_salt = apply_filters('m_router_data/render_key_salt', 'DsHWtvGPGje5kjDetOVWd2CkflKWztdDRAMA7FN4b9tbqkXfozxH0ET7dbB92wRdNZOVBuVUZQWiRiqP');
		
		$generated_key = md5($path.$render_key_salt);
		$rendered_path = $upload_dir['basedir'].'/mrouter-seo-renders/'.md5($generated_key.$salt).'.html';
		if(file_exists($rendered_path)) {
			readfile($rendered_path);
			return;
		}
		
		//METODO: remove this when everything has transitioned
		$permalink = get_permalink();
		$rendered_path = $upload_dir['basedir'].'/mrouter-seo-renders/'.md5($permalink.$salt).'.html';
		
		if(file_exists($rendered_path)) {
			readfile($rendered_path);
		}
	}
	
	function wprr_get_rendered_content($path) {
		$upload_dir = wp_upload_dir(null, false);
		
		$salt = apply_filters('m_router_data/salt', 'wvIUIAULTxKicDpbkzyPpVi5wskSe6Yxy0Uq4wCqbAui1wVKAKmsVhN7JOhGbFQohVs9pnpQoS1dWGkL');
		$render_key_salt = apply_filters('m_router_data/render_key_salt', 'DsHWtvGPGje5kjDetOVWd2CkflKWztdDRAMA7FN4b9tbqkXfozxH0ET7dbB92wRdNZOVBuVUZQWiRiqP');
		
		$generated_key = md5($path.$render_key_salt);
		$rendered_path = $upload_dir['basedir'].'/mrouter-seo-renders/'.md5($generated_key.$salt).'.html';
		if(file_exists($rendered_path)) {
			return file_get_contents($rendered_path);
		}
		
		//METODO: remove this when everything has transitioned
		$permalink = get_permalink();
		$rendered_path = $upload_dir['basedir'].'/mrouter-seo-renders/'.md5($permalink.$salt).'.html';
		
		if(file_exists($rendered_path)) {
			return file_get_contents($rendered_path);
		}
		
		return null;
	}
	
	function mrouter_get_render_settings($path) {
		$settings = array();
		
		//METODO: check if render is needed
		
		$settings['path'] = $path;
		
		$salt = apply_filters('m_router_data/salt', 'wvIUIAULTxKicDpbkzyPpVi5wskSe6Yxy0Uq4wCqbAui1wVKAKmsVhN7JOhGbFQohVs9pnpQoS1dWGkL');
		$render_key_salt = apply_filters('m_router_data/render_key_salt', 'DsHWtvGPGje5kjDetOVWd2CkflKWztdDRAMA7FN4b9tbqkXfozxH0ET7dbB92wRdNZOVBuVUZQWiRiqP');
		
		//METODO: injected key '9I9WQgoWHpVm47Z0wVgeKcmswwyinNHwKIfKH3mI1WLRwt9PPAZE25ylqkiTG1Xsyjx5dWUmn0W7qu2S'
		
		$settings['key'] = md5($path.$render_key_salt);
		
		return $settings;
	}
	
	function mrouter_encode() {
		$encoder = new \Wprr\WprrEncoder();
		
		return $encoder->encode();
	}
	
	function mrouter_encode_post_link($post_id) {
		$encoder = new \Wprr\WprrEncoder();
		
		return $encoder->encode_post_link($post_id);
	}
	
	function mrouter_encode_post($post) {
		$encoder = new \Wprr\WprrEncoder();
		
		return $encoder->encode_post($post);
	}
	
	function mrouter_encode_image($post) {
		$encoder = new \Wprr\WprrEncoder();
		
		return $encoder->encode_image($post);
	}
	
	function mrouter_encode_acf_field($field_object, $post_id) {
		$encoder = new \Wprr\WprrEncoder();
		
		return $encoder->encode_acf_field($field_object, $post_id);
	}
	
	function mrouter_encode_post_acf_field($field_name, $post_id) {
		$encoder = new \Wprr\WprrEncoder();
		
		return $encoder->encode_post_acf_field($field_name, $post_id);
	}
	
	function mrouter_encode_term($term) {
		$encoder = new \Wprr\WprrEncoder();
		
		return $encoder->encode_term($term);
	}
	
	function mrouter_encode_all_taxonomies() {
		
		$return_object = array();
		$encoder = new \Wprr\WprrEncoder();
		
		$taxonomies = get_taxonomies(); 
		foreach($taxonomies as $taxonomy) {
			$encoded_terms = array();
			$terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));
			foreach($terms as $term) {
				$encoded_terms[] = $encoder->encode_term($term);
			}
			$return_object[$taxonomy] = $encoded_terms;
		}
		
		return $return_object;
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
	
	function mrouter_custom_range_query_default_types($query_args, $data) {
		$query_args['post_type'] = apply_filters('m_router_data/custom_range_default_types', array('post', 'page'), $data);
		
		return $query_args;
	}
	
	function mrouter_custom_range_query_taxonomy($query_args, $data) {
		
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

		return $query_args;
		
	}
	
	function wprr_get_configuration_data() {
		$return_array = array();
		
		$return_array['paths'] = apply_filters(M_ROUTER_DATA_DOMAIN.'/'.'configuration_paths', array());
		$return_array['initialMRouterData'] = get_initial_mrouter_data();
		$return_array['imageSizes'] = apply_filters(M_ROUTER_DATA_DOMAIN.'/'.'configuration_image_sizes', array());
		$return_array['userData'] = apply_filters(M_ROUTER_DATA_DOMAIN.'/'.'configuration_user_data_if_logged_in', null);
		$return_array['settings'] = array();
		
		if(is_admin()) {
			$return_array['admin'] = apply_filters(M_ROUTER_DATA_DOMAIN.'/'.'configuration_admin_data', array());
		}
		
		$render_id = -1;
		if(is_singular()) {
			$render_id = get_the_id();
		}
		
		$return_array['renderId'] = $render_id;
		
		if($return_array['userData'] === null) {
			$render_path = $_SERVER["REQUEST_URI"];
			$mrouter_render_settings = mrouter_get_render_settings($render_path);
			$return_array['render'] = $mrouter_render_settings;
		}
		else {
			$return_array['render'] = null;
		}
		
		return apply_filters(M_ROUTER_DATA_DOMAIN.'/'.'configuration', $return_array);
	}
	
	function wprr_output_module_with_custom_data($name, $data, $seo_content = null, $module_data = null) {
		
		$element_id = 'wprr-'.sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
		
		?>
		<div id="<?php echo($element_id); ?>">
			<?php echo($seo_content); ?>
		</div>
		<script>
			window.wprr.insertModule(
				"<?php echo($name); ?>",
				document.querySelector("#<?php echo($element_id); ?>"),
				<?php echo(json_encode($data)); ?>,
				<?php echo(json_encode($module_data)); ?>,
			);
		</script>
		<?php
	}
	
	function wprr_output_module($name, $module_data = null) {
		wprr_output_module_with_custom_data($name, wprr_get_configuration_data(), null, $module_data);
	}
	
	function wprr_output_module_with_seo_content($name, $seo_path, $module_data = null) {
		wprr_output_module_with_custom_data($name, wprr_get_configuration_data(), wprr_get_rendered_content($seo_path), $module_data);
	}
?>