<?php
	namespace Wprr;
	
	use \WP_Query;
	
	// \Wprr\GlobalItemHooks
	class GlobalItemHooks {
		
		function __construct() {
			//echo("\Wprr\GlobalItemHooks::__construct<br />");
			
			
		}
		
		public function register() {
			//echo("\Wprr\GlobalItemHooks::register<br />");
			
			$prefix = WPRR_DOMAIN.'/global-item';
			
			add_filter($prefix.'/development/generate-data-api-settings', array($this, 'filter_development_generate_data_api_settings'), 10, 3);
		}
		
		public function filter_development_generate_data_api_settings($return_object, $item, $data) {
			wprr_generate_data_api_settings();
		}
		
		public static function test_import() {
			echo("Imported \Wprr\GlobalItemHooks<br />");
		}
	}
?>