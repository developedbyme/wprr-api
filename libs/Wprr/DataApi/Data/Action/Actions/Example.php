<?php
	namespace Wprr\DataApi\Data\Action\Actions;

	// \Wprr\DataApi\Data\Action\Actions\Example
	class Example {

		function __construct() {
			
		}
		
		public static function apply_action($return_value, $data) {
			//var_dump("Example::apply_action");
			
			return 'This is an example of an action';
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Example<br />");
		}
	}
?>