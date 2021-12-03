<?php
	require_once("../setup-endpoint.php");
	
	global $wprr_data_api;
	
	$data = array();
	foreach($_GET as $key => $value) {
		$data[$key] = $value;
	}
	
	try {
		if(!isset($_GET['id'])) {
			throw(new \Exception('Parameter id not specified'));
		}
		
		$wprr_data_api->performance()->start_meassure('Taxonomy select');
		$taxonomy_id = $_GET['id'];
		$taxonomy = $wprr_data_api->wordpress()->get_taxonomy($taxonomy_id);
		$wprr_data_api->performance()->stop_meassure('Taxonomy select');
		
		$wprr_data_api->performance()->start_meassure('Taxonomy encode');
		$id = $wprr_data_api->range()->encode_taxonomy($taxonomy);
		$wprr_data_api->performance()->stop_meassure('Taxonomy encode');
		
		$return_data = $wprr_data_api->range()->get_encoded_data()->get_result();
		$return_data['ids'] = array($id);
		
		$wprr_data_api->output()->output_api_repsponse($return_data);
	}
	catch(Exception $error) {
		$wprr_data_api->output()->output_api_error($error->getMessage());
	}
?>