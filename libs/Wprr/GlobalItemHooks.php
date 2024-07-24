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
			add_filter($prefix.'/development/wprr-test', array($this, 'filter_development_wprr_test'), 10, 3);
		}
		
		public function filter_development_generate_data_api_settings($return_object, $item, $data) {
			wprr_generate_data_api_settings();
		}
		
		public function filter_development_wprr_test($return_object, $item, $data) {
			
			
			$editor = wprr_get_data_api()->wordpress()->editor();
			
			$editor->create_object_relation_type('for');
			$editor->create_object_relation_type('in');
			$editor->create_object_relation_types('by', 'based-on');
			
			$editor->create_object_user_relation_type('user-for');
			
		}
		
		public static function test_import() {
			echo("Imported \Wprr\GlobalItemHooks<br />");
		}
	}
?>