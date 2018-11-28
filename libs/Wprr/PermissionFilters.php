<?php
	namespace Wprr;

	class PermissionFilters {
		
		public static function is_admin() {
			if(!is_user_logged_in()) {
				return false;
			}
			$current_user = wp_get_current_user();
			
			if(!in_array('administrator', $current_user->roles)) {
				return false;
			}
			
			return true;
		}
		
		public static function waterfall_is_admin($has_permission) {
			if(!$has_permission) {
				return $has_permission;
			}
			
			return self::is_admin();
		}
		
		public static function test_import() {
			echo("Imported \Wprr\PermissionFilters<br />");
		}
	}
?>
