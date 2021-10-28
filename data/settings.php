<?php
	global $wprr_data_api;
	if(!$wprr_data_api) {
		$wprr_data_api = new Wprr\DataApi\DataApiController();
	}
	
	register_shutdown_function(function() {
		$error = error_get_last();
		
		if($error !== NULL) {
			
			$crash_types = array(E_ERROR, E_PARSE, E_USER_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR);
			
			if(isset($error['type']) && in_array($error['type'], $crash_types, true)) {
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
		}
	});
	
	require_once(WPRR_DIR.'/../../uploads/wprr-api-settings/settings.php');
	require_once(WPRR_DIR.'/../../uploads/wprr-api-settings/register-ranges.php');
?>