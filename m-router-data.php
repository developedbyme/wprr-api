<?php 
	/*
	Plugin Name: mRouter data
	Plugin URI: http://oddalice.se
	Description: Providing data for the mRouter
	Version: 0.8.2
	Author: Odd alice
	Author URI: http://oddalice.se
	*/
	
	define("M_ROUTER_DATA_DOMAIN", "m-router-data");
	define("M_ROUTER_DATA_TEXTDOMAIN", "m-router-data");
	define("M_ROUTER_DATA_MAIN_FILE", __FILE__);
	define("M_ROUTER_DATA_DIR", untrailingslashit(dirname(__FILE__)));
	define("M_ROUTER_DATA_URL", untrailingslashit(plugins_url('',__FILE__)));
	define("M_ROUTER_DATA_VERSION", '0.8.2');
	
	require_once(M_ROUTER_DATA_DIR."/libs/MRouterData/bootstrap.php");

	$MRouterDataPlugin = new \MRouterData\Plugin();

	require_once(M_ROUTER_DATA_DIR."/external-functions.php");
?>