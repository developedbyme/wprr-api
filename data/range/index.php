<?php
	require_once("../setup-endpoint.php");
	
	global $wprr_data_api;
	
	$data = array();
	foreach($_GET as $key => $value) {
		$data[$key] = $value;
	}
	
	$selections = $_GET['select'];
	$ids = $wprr_data_api->range()->select($selections, $data);
	
	$encodings = $_GET['encode'];
	$result = $wprr_data_api->range()->encode_range($ids, $encodings, $data);

	$wprr_data_api->output()->output_api_repsponse($result);
	
	
?>