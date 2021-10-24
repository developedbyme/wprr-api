<?php
	register_shutdown_function( "wprr_data_fatal_report" );
	
	function wprr_data_fatal_report() {
		$error = error_get_last();
		
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
	}


	global $wprr_data_api;
	if(!$wprr_data_api) {
		$wprr_data_api = new Wprr\DataApi\DataApiController();
	}

	require_once(WPRR_DIR.'/../../uploads/wprr-api-settings/settings.php');
	
?>