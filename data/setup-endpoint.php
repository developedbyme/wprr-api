<?php
	error_reporting(E_ALL);
	
	if(isset($_GET['extendedMemory'])) {
		ini_set('memory_limit', $_GET['extendedMemory']);
	}
	
	
	require_once(dirname(__FILE__)."/../setup.php");
	require_once(dirname(__FILE__)."/settings.php");
	
?>