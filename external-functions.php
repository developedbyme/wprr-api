<?php
	function wprr_performance_tracker() {
		return wprr_get_data_api()->performance();
	}
	
	function wprr_get_configuration_data() {
		$return_array = array();
		
		$return_array['paths'] = apply_filters(WPRR_DOMAIN.'/'.'configuration_paths', array());
		$return_array['initialMRouterData'] = array('data' => array());
		$return_array['imageSizes'] = apply_filters(WPRR_DOMAIN.'/'.'configuration_image_sizes', array());
		$return_array['userData'] = null;
		$return_array['settings'] = array();
		
		if(is_admin()) {
			$return_array['admin'] = apply_filters(WPRR_DOMAIN.'/'.'configuration_admin_data', array());
		}
		
		return apply_filters(WPRR_DOMAIN.'/'.'configuration', $return_array);
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
	
	function wprr_output_module_with_seo_content($name, $seo_path, $module_data = null, $classes = null) {
		wprr_output_module($name, $module_data, $classes);
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
	
	
	function wprr_ensure_wc_has_cart() {
		\Wprr\Core\Utils\WoocommerceFunctions::ensure_wc_has_cart();
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
		return '$range_controller->register_selection("'.$type.'", "'.$file_path.'", "'.$class_path.'");';
	}
	
	function wprr_get_data_api_encode_registration_code($type, $file_path, $class_path) {
		return '$range_controller->register_encoding(\''.$type.'\', \''.$file_path.'\', \''.$class_path.'\');';
	}
	
	function wprr_get_data_api_data_function_registration_code($type, $file_path, $class_path) {
		return '$range_controller->register_data_function(\''.$type.'\', \''.$file_path.'\', \''.$class_path.'\');';
	}
	
	function wprr_get_data_api_registry_registration_code($id, $value) {
		return '$wprr_data_api->registry()->add_to_array(\''.$id.'\', '.$value.');';
	}
	
	function wprr_get_data_api_auto_loader_registration_code($namespace, $directory) {
		return '$wprr_data_api->auto_loader()->add_auto_loader(\''.$namespace.'\', \''.$directory.'\');';
	}
	
	function wprr_get_data_api() {
		require_once(WPRR_DIR."/data/settings-wp.php");
		
		global $wprr_data_api;
		return $wprr_data_api;
	}
?>