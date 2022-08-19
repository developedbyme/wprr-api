<?php
	require_once("../setup-endpoint.php");
	
	global $wprr_data_api;
	$user = $wprr_data_api->user();
	
	if($user->is_signed_in()) {
		
		$data = array(
			'user' => $user->get_me_data(),
			'restNonce' => $user->get_rest_nonce()
		);
		
		$wprr_data_api->output()->output_api_repsponse($data);
	}
	else {
		$wprr_data_api->output()->output_api_repsponse(null);
	}
?>