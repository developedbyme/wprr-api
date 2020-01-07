<?php
	namespace Wprr\RestApi;
	
	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\RestApi\GetMenuEndPoint
	class GetMenuEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\GetMenuEndPoint::__construct<br />");
			
			
		}
		
		protected function encode_menu_item($menu_item, $all_items) {
			
			$menu_item_id = (int)$menu_item->ID;
			
			$menu_array = array(
				"id"        => $menu_item_id,
				"title"     => $menu_item->title,
				"alt"       => $menu_item->attr_title,
				"slug"      => sanitize_title( $menu_item->title ),
				"link"      => $menu_item->url,
				"target"    => $menu_item->target,
				"order"     => $menu_item->menu_order,
				"css_classes" => $menu_item->classes,
				"children"  => $this->encode_children($all_items, $menu_item_id)
			);
			
			return $menu_array;
		}
		
		protected function encode_children($all_items, $parent_id = 0) {
			
			$return_array = array();
			
			foreach($all_items as $menu_item) {
				$current_parent_id = (int)$menu_item->menu_item_parent;
				if($current_parent_id === $parent_id) {
					$return_array[] = $this->encode_menu_item($menu_item, $all_items);
				}
			}
			
			return $return_array;
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\GetMenuEndPoint::perform_call<br />");
			
			$return_array = array();
			
			do_action(M_ROUTER_DATA_DOMAIN.'/prepare_api_request', $data);
			
			// Get the menu data from WP
			$menu_name = sanitize_text_field( $data["location"] );
			$locations = get_nav_menu_locations();
			
			if(!isset($locations[$menu_name])) {
				return $this->output_error("No menu for that location");
			}
			
			$menu_id = $locations[$menu_name];
			$menu_items = wp_get_nav_menu_items($menu_id);
			
			if($menu_items) {
				$return_array = $this->encode_children($menu_items);
				return $this->output_success($return_array);
			}
			
			return $this->output_success(array());
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\GetMenuEndPoint<br />");
		}
	}
?>