<?php
	global $wprr_data_api;
	if(!$wprr_data_api) {
		$wprr_data_api = new Wprr\DataApi\DataApiController();
	}
	
	define('READ_OBJECT_RELATION_TABLES', false);
	define('WRITE_OBJECT_RELATION_TABLES', false);
	define('SKIP_OBJECT_RELATION_META', false);
	
?>