<?php
	require_once("../setup-endpoint.php");
	
	global $wprr_data_api;
	
	$data = array();
	foreach($_GET as $key => $value) {
		$data[$key] = $value;
	}
	
	try {
		$user = $wprr_data_api->user()->get_user_for_call($_GET);
		$is_ok = $user->is_trusted();
		if(!$is_ok) {
			throw(new \Exception('User '.$user->get_id().' is not allowed to use admin function'));
		}
		
		$user_rows = $wprr_data_api->database()->query_without_storage('SELECT ID as id FROM '.DB_TABLE_PREFIX.'users');
		$ids = array_map(function($item) {return $item['id'];}, $user_rows);
		
		$result = $wprr_data_api->range()->encode_user_range($ids, $data);
		
		$wprr_data_api->output()->output_api_repsponse($result);
	}
	catch(Exception $error) {
		$wprr_data_api->output()->output_api_error($error->getMessage());
	}
?>