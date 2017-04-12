<?php
	//MENOTE: this plugin doesn't have any functions that can be called externally
	
	function mrouter_encode() {
		$encoder = new \MRouterData\MRouterDataEncoder();
		
		return $encoder->encode();
	}
	
	function mrouter_encode_post_link($post_id) {
		$encoder = new \MRouterData\MRouterDataEncoder();
		
		return $encoder->encode_post_link($post_id);
	}
	
	function mrouter_encode_term($term) {
		$encoder = new \MRouterData\MRouterDataEncoder();
		
		return $encoder->encode_term($term);
	}
	
	function mrouter_disable_all_ranges($filter_priority = 10) {
		add_filter('m_router_data/range_has_permission', function($has_permission) {return false;}, $filter_priority, 1);
	}
	
	global $mrouter_disabled_post_types;
	$mrouter_disabled_post_types = array();
	
	function mrouter_disable_data_for_post_type($post_type, $filter_priority = 10) {
		
		global $mrouter_disabled_post_types;
		$mrouter_disabled_post_types[] = $post_type;
		
		add_filter('m_router_data/range_has_permission_'.$post_type, function($has_permission) {return false;}, $filter_priority, 1);
		add_filter('m_router_data/id_has_permission', function($has_permission, $id) {
			if(!$has_permission) {
				return $has_permission;
			}
			
			global $mrouter_disabled_post_types;
			$post_type = get_post_type($id);
			
			if(in_array($post_type, $mrouter_disabled_post_types)) {
				return false;
			}
			return true;
		}, $filter_priority, 2);
	}
?>