<?php

function MRouterData_Autoloader( $class ) {
	//echo("MRouterData_Autoloader<br />");
	
	$namespace_length = strlen("MRouterData");
	
	// Is a MRouterData class
	if ( substr( $class, 0, $namespace_length ) != "MRouterData" ) {
		return false;
	}

	// Uses namespace
	if ( substr( $class, 0, $namespace_length+1 ) == "MRouterData\\" ) {

		$path = explode( "\\", $class );
		unset( $path[0] );

		$class_file = trailingslashit( dirname( __FILE__ ) ) . implode( "/", $path ) . ".php";

	}

	// Doesn't use namespaces
	elseIf ( substr( $class, 0, $namespace_length+1 ) == "MRouterData_" ) {

		$path = explode( "_", $class );
		unset( $path[0] );

		$class_file = trailingslashit( dirname( __FILE__ ) ) . implode( "/", $path ) . ".php";

	}

	// Get class
	if ( isset($class_file) && is_file( $class_file ) ) {

		require_once( $class_file );
		return true;

	}

	// Fallback to error
	return false;

}

spl_autoload_register("MRouterData_Autoloader"); // Register autoloader