<?php
	require_once("../setup-endpoint.php");
	
	global $wprr_data_api;
	
	$data = array();
	foreach($_GET as $key => $value) {
		$data[$key] = $value;
	}
	
	try {
		if(!isset($_GET['url'])) {
			throw(new \Exception('Parameter url not specified'));
		}
		
		$result = array();
		
		$url = parse_url($_GET['url']);
		
		$path = trim($url['path'], "/");
		
		$site_url = parse_url(SITE_URL);
		
		if(isset($site_url['path'])) {
			$site_path = trim($site_url['path'], "/");
			if(strpos($site_path, $path) === 0) {
				$path = trim(substr($path, strlen($site_path)), "/");
			}
			else {
				throw(new \Exception('Not in base path'));
			}
		}
		
		$result['path'] = $path;
		
		$post_id = $wprr_data_api->wordpress()->get_post_id_by_path($path);
		
		$result['pageType'] = "missing";
		
		if($post_id) {
			$result['pageType'] = "page";
			
			$language = $wprr_data_api->wordpress()->get_language_by_path($path);
			if(!$language) {
				$language = $wprr_data_api->wordpress()->get_post($post_id)->get_meta('language');
			}
			
			$result['language'] = $language;
			$result['posts'] = $wprr_data_api->range()->encode_range(array($post_id), 'page,pageTemplate', $data);
		}
		
		$wprr_data_api->output()->output_api_repsponse($result);
	}
	catch(Exception $error) {
		$wprr_data_api->output()->output_api_error($error->getMessage());
	}
?>