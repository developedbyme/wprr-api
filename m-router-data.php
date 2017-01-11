<?php 
	/*
	Plugin Name: mRouter data
	Plugin URI: http://oddalice.se
	Description: Providing data for the mRouter
	Version: 0.1.1
	Author: Odd alice
	Author URI: http://oddalice.se
	*/
	
	define("M_ROUTER_DATA_DOMAIN", "m-rouuter-data");
	define("M_ROUTER_DATA_TEXTDOMAIN", "m-rouuter-data");
	define("M_ROUTER_DATA_MAIN_FILE", __FILE__);
	define("M_ROUTER_DATA_DIR", untrailingslashit( dirname( __FILE__ )  ) );
	define("M_ROUTER_DATA_URL", untrailingslashit( plugins_url('',  __FILE__ )  ) );
	define("M_ROUTER_DATA_VERSION", '0.1.1');
	
	function m_router_data_template_redirect() {
		if(isset($_GET['mRouterData']) && $_GET['mRouterData'] === 'json') {
			
			global $wp_query;
			
			$data = array();
			
			$data['m_router'] = array('version' => M_ROUTER_DATA_VERSION);
			$data['queried_object'] = get_queried_object();
			$data['query'] = $wp_query;
			
			header('Content-Type: application/json');
			header("Access-Control-Allow-Origin: *");
			echo(json_encode($data));
			
			exit();
		}
	}
	
	add_action('template_redirect', 'm_router_data_template_redirect');
?>