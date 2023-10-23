<?php
	require_once("../setup-endpoint.php");
	
	global $wprr_data_api;
	
	$data = array();
	foreach($_GET as $key => $value) {
		$data[$key] = $value;
	}
	
	try {
		if(!isset($_GET['type'])) {
			throw(new \Exception('Parameter type not specified'));
		}
		
		$reposnse_type = isset($_GET['responseType']) ? $_GET['responseType'] : 'message';
		if($reposnse_type === 'redirect') {
			if(!isset($_GET['redirectTo'])) {
				throw(new \Exception('Parameter redirectTo not specified'));
			}
		}
		
		$wprr_data_api->performance()->start_meassure('Data get_data');
		$type = $_GET['type'];
		$result = $wprr_data_api->action()->perform($type, $data);
		$wprr_data_api->performance()->stop_meassure('Data get_data');
		
		switch($reposnse_type) {
			case 'redirect':
				$wprr_data_api->output()->redirect($_GET['redirectTo']);
			case 'redirectToResponse':
				$wprr_data_api->output()->redirect($result);
			case 'json':
				$wprr_data_api->output()->output_api_repsponse($result);
			case 'data':
				if($result['format'] === 'file') {
					if(isset($result['meta']['contentType'])) {
						header('Content-Type: '.$result['meta']['contentType']);
					}
					$wprr_data_api->output()->output_response($result['data']);
				}
				else if($result['format'] === 'redirect') {
					$wprr_data_api->output()->redirect($result['data']);
				}
				$wprr_data_api->output()->output_response($result);
			default:
			case 'message':
				$wprr_data_api->output()->redirect_to_action_complete();
		}
		
	}
	catch(Exception $error) {
		$wprr_data_api->output()->output_error($error->getMessage());
	}
?>