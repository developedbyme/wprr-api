<?php
	require_once("../setup-end-point.php");
	
	require_once("../../setup.php");
	require_once("../settings.php");
	
	global $wprr_data_api;
	$user = $wprr_data_api->user();
	
	if($user->is_signed_in()) {
		$data = array(
			'user' => $user->get_user_data(),
			'restNonce' => $user->get_rest_nonce()
		);
		
		$wprr_data_api->output()->output_api_repsponse($data);
	}
	else {
		$wprr_data_api->output()->output_api_repsponse(null);
	}
?>