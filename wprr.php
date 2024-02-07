<?php 
	/*
	Plugin Name: WPRR API
	Plugin URI: http://wpreactrouter.com
	Description: Endpoints and functionality for the WP react router.
	Version: 2.0.0
	Author: Mattias Ekendahl
	Author URI: http://developedbyme.com
	*/
	
	define("WPRR_DOMAIN", "wprr");
	define("WPRR_TEXTDOMAIN", "wprr");
	define("WPRR_MAIN_FILE", __FILE__);
	define("WPRR_DIR", untrailingslashit(dirname(__FILE__)));
	define("WPRR_URL", untrailingslashit(plugins_url('',__FILE__)));
	define("WPRR_VERSION", '2.0.0');
	
	require_once(WPRR_DIR."/libs/Wprr/bootstrap.php");
	
	$WprrPlugin = new \Wprr\Plugin();
	
	global $WprrPerformanceTracker;
	$WprrPerformanceTracker = new \Wprr\PerformanceTracker();
	
	require_once(WPRR_DIR."/external-functions.php");
	
?>