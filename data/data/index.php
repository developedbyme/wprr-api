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
		
		$wprr_data_api->performance()->start_meassure('Data get_data');
		$type = $_GET['type'];
		$result = $wprr_data_api->range()->get_data($type, $data);
		$wprr_data_api->performance()->stop_meassure('Data get_data');
		
		$wprr_data_api->output()->output_api_repsponse($result);
	}
	catch(Exception $error) {
		$wprr_data_api->output()->output_api_error($error->getMessage());
	}
?>