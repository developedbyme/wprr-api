<?php

function Wprr_Data_Autoloader( $class ) {
	//echo("Wprr_Data_Autoloader<br />");
	
	$namespace_length = strlen("Wprr");
	
	// Is a Wprr class
	if ( substr( $class, 0, $namespace_length ) != "Wprr" ) {
		return false;
	}

	// Uses namespace
	if ( substr( $class, 0, $namespace_length+1 ) == "Wprr\\" ) {

		$path = explode( "\\", $class );
		unset( $path[0] );

		$class_file = dirname( __FILE__ ) . '/' . implode( "/", $path ) . ".php";

	}
	
	// Get class
	if ( isset($class_file) && is_file( $class_file ) ) {

		require_once( $class_file );
		return true;

	}

	// Fallback to error
	return false;

}

spl_autoload_register("Wprr_Data_Autoloader"); // Register autoloader