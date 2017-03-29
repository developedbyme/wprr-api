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
?>