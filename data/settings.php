<?php
	global $wprr_data_api;
	if(!$wprr_data_api) {
		$wprr_data_api = new Wprr\DataApi\DataApiController();
	}
	
	function wprr_get_data_api() {
		global $wprr_data_api;
		return $wprr_data_api;
	}
	
	register_shutdown_function(function() {
		$error = error_get_last();
		
		global $wprr_data_api;
		
		if(!$wprr_data_api->output()->has_output()) {
			if($error !== NULL) {
				$error_file = $error["file"];
				$error_line = $error["line"];
				$message = $error["message"];
		
				header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
				header('Content-Type: application/json; charset=utf-8');
		
				$reponse = array(
					'code' => 'error',
					'data' => null,
					'message' => $message,
					'error' => array(
						'file' => $error["file"],
						'line' => $error["line"]
					)
				);
		
				echo(json_encode($reponse));
			}
			else {
				$reponse = array(
					'code' => 'noResponse',
					'data' => null,
					'message' => 'No response for call'
				);
		
				echo(json_encode($reponse));
			}
		}
		
		
	});
	
	require_once(WPRR_DIR.'/../../uploads/wprr-api-settings/settings.php');
	require_once(WPRR_DIR.'/../../uploads/wprr-api-settings/register-ranges.php');
?>