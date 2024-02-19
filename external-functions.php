<?php
	//use \WP_REST_Request;
	
	global $WprrDefaultEncoder;
	$WprrDefaultEncoder = new \Wprr\WprrEncoder();
	
	function wprr_get_encoder() {
		global $WprrDefaultEncoder;
		return $WprrDefaultEncoder;
	}
	
	function wprr_performance_tracker() {
		global $WprrPerformanceTracker;
		return $WprrPerformanceTracker;
	}
	
	function wprr_get_logger() {
		return new \Wprr\Logger();
	}
	
	function get_initial_mrouter_data() {
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		
		$initial_mrouter_data = array();
		$initial_mrouter_data['data'] = array();
		
		$encode_initial_data = apply_filters(WPRR_DOMAIN.'/'.'encode_initial_data_when_render', true);
		if($encode_initial_data) {
			$current_data = mrouter_encode();
			$initial_mrouter_data['data'][$protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']] = array(
				'status' => 1,
				'data' => $current_data['data']
			);
			
			$output_php = apply_filters(WPRR_DOMAIN.'/'.'output_php_messages', false);
			if($output_php) {
				echo($current_data['metadata']['phpOutput']);
			}
		
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
		return wprr_get_encoder()->encode();
	}
	
	function mrouter_encode_post_link($post_id) {
		return wprr_encode_post_link($post_id);
	}
	
	function mrouter_encode_post($post) {
		return wprr_encode_post($post);
	}
	
	function wprr_encode_post_link($post_id) {
		return wprr_get_encoder()->encode_post_link($post_id);
	}
	
	function wprr_encode_private_post_link($post_id) {
		return wprr_get_encoder()->encode_private_post_link($post_id);
	}
	
	function wprr_encode_post($post) {
		return wprr_get_encoder()->encode_post($post);
	}
	
	function wprr_encode_image($post) {
		return wprr_get_encoder()->encode_image($post);
	}
	
	function mrouter_encode_image($post) {
		return wprr_encode_image($post);
	}
	
	function wprr_encode_post_image($post_id) {
		$encoder = wprr_get_encoder();
		
		$media_post_id = get_post_thumbnail_id($post_id);
		if($media_post_id) {
			$media_post = get_post($media_post_id);

			return wprr_encode_image($media_post);
		}
		
		return null;
	}
	
	function mrouter_encode_acf_field($field_object, $post_id) {
		return wprr_get_encoder()->encode_acf_field($field_object, $post_id);
	}
	
	function mrouter_encode_post_acf_field($field_name, $post_id) {
		return wprr_get_encoder()->encode_post_acf_field($field_name, $post_id);
	}
	
	function mrouter_encode_term($term) {
		return wprr_encode_term($term);
	}
	
	function wprr_encode_term($term) {
		return wprr_get_encoder()->encode_term($term);
	}
	
	function wprr_encode_term_by_id($term_id, $taxonomy) {
		return wprr_get_encoder()->encode_term(get_term_by('id', $term_id, $taxonomy));
	}
	
	function wprr_encode_terms_by_id($term_ids, $taxonomy) {
		$encoder = wprr_get_encoder();
		
		$return_array = array();
		foreach($term_ids as $term_id) {
			$return_array[] = $encoder->encode_term(get_term_by('id', $term_id, $taxonomy));
		}
		
		return $return_array;
	}
	
	function wprr_encode_user($user) {
		return wprr_get_encoder()->encode_user($user);
	}
	
	function wprr_encode_range_item($type, $encoded_object, $id, $request_data = null) {
		$encoded_object = apply_filters('wprr/range_encoding/'.$type, $encoded_object, $id, $request_data);
		
		return $encoded_object;
	}
	
	function wprr_encode_range_item_from_id($id, $types, $request_data = null) {
		$types = explode(',', $types);
		
		$encoded_object = array('id' => $id);
		
		foreach($types as $type) {
			$encoded_object = wprr_encode_range_item($type, $encoded_object, $id, $request_data);
		}
		
		return $encoded_object;
	}
	
	function wprr_encode_range_items_from_ids($ids, $types, $request_data = null) {
		$return_array = array();
		
		$types = explode(',', $types);
		
		foreach($ids as $id) {
			$encoded_object = array('id' => $id);
			
			foreach($types as $type) {
				$encoded_object = wprr_encode_range_item($type, $encoded_object, $id, $request_data);
			}
			
			$return_array[] = $encoded_object;
		}
		
		return $return_array;
	}
	
	function mrouter_encode_all_taxonomies() {
		
		$return_object = array();
		$encoder = wprr_get_encoder();
		
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
		
		$encode_initial_data = apply_filters(WPRR_DOMAIN.'/'.'encode_initial_data_when_render', true);
		
		$return_array['paths'] = apply_filters(M_ROUTER_DATA_DOMAIN.'/'.'configuration_paths', array());
		$return_array['initialMRouterData'] = get_initial_mrouter_data();
		$return_array['imageSizes'] = apply_filters(M_ROUTER_DATA_DOMAIN.'/'.'configuration_image_sizes', array());
		$return_array['userData'] = apply_filters(M_ROUTER_DATA_DOMAIN.'/'.'configuration_user_data_if_logged_in', null);
		$return_array['settings'] = array();
		
		if(is_admin()) {
			$return_array['admin'] = apply_filters(M_ROUTER_DATA_DOMAIN.'/'.'configuration_admin_data', array());
		}
		
		if($encode_initial_data) {
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
		}
		
		return apply_filters(M_ROUTER_DATA_DOMAIN.'/'.'configuration', $return_array);
	}
	
	function wprr_get_code_for_module_with_custom_data($name, $data, $seo_content = null, $module_data = null, $classes = null) {
		
		$started = ob_start();
		if(!$started) {
			throw(new \Exception('Couldn\'t start buffer'));
		}
		
		wprr_output_module_with_custom_data($name, $data, $seo_content, $module_data, $classes);
		
		$return_string = ob_get_contents();
		ob_end_clean();
		
		return $return_string;
	}
	
	function wprr_output_module_with_custom_data($name, $data, $seo_content = null, $module_data = null, $classes = null) {
		
		$element_id = 'wprr-'.sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
		
		$extra_attributes = '';
		if($classes) {
			$extra_attributes .= ' class="'.$classes.'"';
		}
		
		?>
		<div id="<?php echo($element_id); ?>"<?php echo($extra_attributes); ?>>
			<?php echo($seo_content); ?>
		</div>
		<script>
			window.wprr.insertModule(
				"<?php echo($name); ?>",
				document.querySelector("#<?php echo($element_id); ?>"),
				<?php echo(json_encode($data)); ?>,
				<?php echo(json_encode($module_data)); ?>
			);
		</script>
		<?php
	}
	
	function wprr_output_module($name, $module_data = null, $classes = null) {
		wprr_output_module_with_custom_data($name, wprr_get_configuration_data(), null, $module_data, $classes);
	}
	
	function wprr_get_preloaded_data_for_url($url) {
		
		$return_array = array();
		
		$salt = apply_filters('wprr/initial-load-cache/salt', 'wvIUIAULTxKicDpbkzyPpVi5wskSe6Yxy0Uq4wCqbAui1wVKAKmsVhN7JOhGbFQohVs9pnpQoS1dWGkL');
		
		$upload_dir = wp_upload_dir(null, false);
		$path = $upload_dir['basedir'].'/wprr-initial-load-cache/'.md5($url.$salt).'.json';
		
		if(file_exists($path)) {
			
			$api_calls = json_decode(file_get_contents($path), true);
			
			if($api_calls) {
				$rest_server = rest_get_server();
			
				foreach($api_calls as $api_call) {
					$current_url = $api_call;
					$api_request = \WP_REST_Request::from_url($current_url);
					
					$start_time_part = microtime(true);
					$api_response = $rest_server->dispatch($api_request);
					$end_time_part = microtime(true);
					
					//METODO: check for ok response
					$return_array[$current_url] = array(
						'status' => 1,
						'performance' => $end_time_part-$start_time_part,
						'data' => $api_response->data['data']
					);
				}
			}
		}
		
		return $return_array;
	}
	
	function wprr_output_module_with_seo_content($name, $seo_path, $module_data = null, $classes = null) {
		
		$configuration_data = wprr_get_configuration_data();
		
		$encode_initial_data = apply_filters(WPRR_DOMAIN.'/'.'encode_initial_data_when_render', true);
		if($encode_initial_data) {
			$configuration_data['preloadedData'] = wprr_get_preloaded_data_for_url(home_url($seo_path));
		}
		
		wprr_output_module_with_custom_data($name, $configuration_data, wprr_get_rendered_content($seo_path), $module_data, $classes);
	}
	
	function wprr_apply_post_changes($post_id, $changes, $logger = null) {
		
		//Check that we are allowed to change the post
		foreach($changes as $change) {
			$change_type = $change['type'];
			$change_data = $change['data'];
			
			//METODO: check if change is allowed
			$action_path = WPRR_DOMAIN.'/admin/change_post/'.$change_type;
			
			if($logger) {
				$logger->add_log('Applying change '.$change_type);
			}
			
			if(has_action($action_path)) {
				do_action($action_path, $change_data, $post_id, $logger);
			}
			else {
				if($logger) {
					$logger->add_log('No change function for type '.$change_type);
				}
			}
			
		}
	}
	
	function wprr_get_page_breadcrumb($post_id) {
		
		$parent_id = wp_get_post_parent_id($post_id);
		if($parent_id) {
			$return_array = wprr_get_page_breadcrumb($parent_id);
		}
		else {
			$return_array = array();
		}
		
		$return_array[] = mrouter_encode_post_link($post_id);
		
		return $return_array;
	}
	
	function wprr_ensure_wc_has_cart() {
		\Wprr\OddCore\Utils\WoocommerceFunctions::ensure_wc_has_cart();
	}
	
	function wprr_enqueue_admin_data($script_name) {
		wp_localize_script($script_name, 'wprrAdminData', wprr_get_admin_data());
	}
	
	function wprr_get_admin_data() {
		$screen = get_current_screen();
		
		$localized_data = array(
			'screen' => $screen,
			'restApiBaseUrl' => get_home_url().'/wp-json/'
		);
	
		$postData = null;
		if($screen && $screen->base === 'post') {
			$postData = mrouter_encode_post(get_post());
		}
	
		$localized_data['postData'] = $postData;
		
		$localized_data['taxonomies'] = mrouter_encode_all_taxonomies();
		
		return $localized_data;
	}
	
	function wprr_get_id_in_current_language($id) {
		global $sitepress;
		if($sitepress) {
			$id = apply_filters('wpml_object_id', $id, 'post', true, $sitepress->get_current_language());
		}
		
		return $id;
	}
	
	function wprr_get_current_langauge() {
		global $sitepress;
		
		if(isset($sitepress)) {
			return $sitepress->get_current_language();
		}
		
		return substr(get_locale(), 0, 2);
	}
	
	function wprr_encode_item_as($encoding, $encoded_data, $post_id) {
		
		$encodings = explode(',', $encoding);
		
		foreach($encodings as $current_encoding) {
			$filter_name = WPRR_DOMAIN.'/range_encoding/'.$current_encoding;
		
			$encoded_data = apply_filters($filter_name, $encoded_data, $post_id, null);
		}
		
		return $encoded_data;
	}
	
	function wprr_encode_item_as_by_id($encoding, $post_id) {
		$current_data = array('id' => $post_id);
		return wprr_encode_item_as($encoding, $current_data, $post_id);
	}
	
	function wprr_encode_items_as_by_id($encoding, $post_ids) {
		
		$return_array = array();
		
		foreach($post_ids as $post_id) {
			$return_array[] = wprr_encode_item_as_by_id($encoding, $post_id);
		}
		
		return $return_array;
	}
	
	function wprr_get_combination_id(...$ids) {
		natsort($ids);
		
		return implode('-', $ids);
	}
	
	function wprr_generate_data_api_settings() {
		$settings = apply_filters('wprr/data-api/generate-settings', '');
		$ranges = '';
		
		$ranges = apply_filters('wprr/data-api/generate-ranges', $ranges);
		
		$settings = "<?php"."\n".$settings."?>";
		$ranges = "<?php"."\n".$ranges."?>";
		
		$upload_dir = wp_upload_dir(null, false);
		
		$upload_path = $upload_dir['basedir'].'/wprr-api-settings';
		
		if (!file_exists($upload_path)) {
			mkdir($upload_path, 0775, true);
		}
		
		if(file_exists($upload_path.'/settings.php')) {
			copy($upload_path.'/settings.php', $upload_path.'/settings-'.time().'.php');
		}
		file_put_contents($upload_path.'/settings.php', $settings);
		
		if(file_exists($upload_path.'/register-ranges.php')) {
			copy($upload_path.'/register-ranges.php', $upload_path.'/register-ranges-'.time().'.php');
		}
		file_put_contents($upload_path.'/register-ranges.php', $ranges);
	}
	
	function wprr_get_data_api_select_registration_code($type, $file_path, $class_path) {
		return '$range_controller->register_selection("'.$type.'", "'.str_replace('\\', '/', $file_path).'", "'.$class_path.'");';
	}
	
	function wprr_get_data_api_encode_registration_code($type, $file_path, $class_path) {
		return '$range_controller->register_encoding(\''.$type.'\', \''.str_replace('\\', '/', $file_path).'\', \''.$class_path.'\');';
	}
	
	function wprr_get_data_api_data_function_registration_code($type, $file_path, $class_path) {
		return '$range_controller->register_data_function(\''.$type.'\', \''.str_replace('\\', '/', $file_path).'\', \''.$class_path.'\');';
	}
	
	function wprr_get_data_api_registry_registration_code($id, $value) {
		return '$wprr_data_api->registry()->add_to_array(\''.$id.'\', '.$value.');';
	}
	
	function wprr_get_data_api_auto_loader_registration_code($namespace, $directory) {
		return '$wprr_data_api->auto_loader()->add_auto_loader(\''.$namespace.'\', \''.str_replace('\\', '/', $directory).'\');';
	}
	
	function wprr_get_data_api() {
		require_once(WPRR_DIR."/data/settings-wp.php");
		
		global $wprr_data_api;
		return $wprr_data_api;
	}
?>