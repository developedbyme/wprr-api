<?php
	namespace MRouterData\RestApi;
	
	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;
	
	// \MRouterData\RestApi\GetMenuEndPoint
	class GetMenuEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\GetMenuEndPoint::__construct<br />");
			
			
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
			  $menu_items = wp_get_nav_menu_items( $menu_id );

			  // Loop thrugh the data from the menu
			  if ( $menu_items ) :
			    foreach ( $menu_items as $menu_item ) :
			      $menu_array = array(
			        "id"        => $menu_item->ID,
			        "title"     => $menu_item->title,
			        "alt"       => $menu_item->attr_title,
			        "slug"      => sanitize_title( $menu_item->title ),
			        "link"      => $menu_item->url,
			        "target"    => $menu_item->target,
			        "order"     => $menu_item->menu_order,
			        "css_classes" => $menu_item->classes,
			        "children"  => array()
			      );

			      // Check if the menu item is a child of another menu item
			      if ( $menu_item->menu_item_parent == 0 ) :
			        $return_array[] = $menu_array;
			      else :
			        // If found, add it to the menu items children
			        foreach ( $return_array as $return_item_key => $return_item ) :
			          if ( $return_item["id"] == $menu_item->menu_item_parent ) :
			            $return_array[$return_item_key]["children"][] = $menu_array;
			          endif;
			        endforeach;
			      endif;
			    endforeach;
			  endif;
			
			return $this->output_success($return_array);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\GetMenuEndPoint<br />");
		}
	}
?>