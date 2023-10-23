<?php
	namespace Wprr\DataApi\Data\Action;

	// \Wprr\DataApi\Data\Action\ActionController
	class ActionController {

		function __construct() {
			
		}
		
		public function perform($type, $data) {
			global $wprr_data_api;
			
			$filters = $wprr_data_api->registry()->get_array('action/'.$type);
			
			if(empty($filters)) {
				throw(new \Exception('No action named '.$type));
			}
			
			$result = null;
			
			foreach($filters as $callable) {
				
				if(!is_callable($callable)) {
					throw(new \Exception('Can\'t call '.$callable));
				}
				
				$result = call_user_func_array($callable, array($result, $data));
			}
			
			return $result;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ActionController<br />");
		}
	}
?>