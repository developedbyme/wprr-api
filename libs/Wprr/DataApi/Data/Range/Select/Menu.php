<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\Menu
	class Menu {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("Menu::select");
			
			global $wprr_data_api;
			
			if(!isset($data['location'])) {
				throw(new \Exception('Menu location not set'));
			}
			
			$theme_name = THEME_NAME;
			$theme_mods = unserialize($wprr_data_api->database()->query_first('SELECT option_value FROM wp_options WHERE option_name = "theme_mods_'.$theme_name.'"')['option_value']);
			
			if(!isset($theme_mods['nav_menu_locations'][$data['location']])) {
				throw(new \Exception('No menu at location '.$data['location']));
			}
			
			$menu_id = $theme_mods['nav_menu_locations'][$data['location']];
			
			$term = $wprr_data_api->wordpress()->get_taxonomy('nav_menu')->get_term_by_id($menu_id);
			$query->include_only($term->get_ids());
		}
		
		public function filter($posts, $data) {
			//var_dump("Menu::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Menu<br />");
		}
	}
?>