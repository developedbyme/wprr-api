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
	
	function mrouter_disable_all_ranges($priority = 10) {
		add_filter('m_router_data/range_has_permission', function($has_permission) {return false;}, $priority, 1);
	}
?>