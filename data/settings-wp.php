<?php
	global $wprr_data_api;
	if(!$wprr_data_api) {
		$wprr_data_api = new Wprr\DataApi\DataApiController();
	}
	
	include_once(WPRR_DIR.'/../../uploads/wprr-api-settings/settings.php');
	include_once(WPRR_DIR.'/../../uploads/wprr-api-settings/register-ranges.php');
?>