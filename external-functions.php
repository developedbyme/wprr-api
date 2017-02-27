<?php
	//MENOTE: this plugin doesn't have any functions that can be called externally
	
	function mrouter_encode() {
		$encoder = new \MRouterData\MRouterDataEncoder();
		
		return $encoder->encode();
	}
?>