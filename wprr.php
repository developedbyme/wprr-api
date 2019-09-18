<?php 
	/*
	Plugin Name: WPRR API
	Plugin URI: http://wpreactrouter.com
	Description: Endpoints and functionality for the WP react router.
	Version: 0.17.7
	Author: Mattias Ekendahl
	Author URI: http://developedbyme.com
	*/
	
	define("WPRR_DOMAIN", "wprr");
	define("M_ROUTER_DATA_DOMAIN", "m_router_data");
	define("WPRR_TEXTDOMAIN", "wprr");
	define("WPRR_MAIN_FILE", __FILE__);
	define("WPRR_DIR", untrailingslashit(dirname(__FILE__)));
	define("WPRR_URL", untrailingslashit(plugins_url('',__FILE__)));
	define("WPRR_VERSION", '0.17.7');
	
	require_once(WPRR_DIR."/libs/Wprr/bootstrap.php");

	$WprrPlugin = new \Wprr\Plugin();

	require_once(WPRR_DIR."/external-functions.php");
	
	add_action('woocommerce_init', function() {
		$need_cart = array_key_exists( 'rest_route', $_REQUEST ) || false !== strpos( $_SERVER['REQUEST_URI'], 'wp-json' );
		
		if($need_cart) {
			include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
			include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
			wc_load_cart();
		}
	}, 1);
?>