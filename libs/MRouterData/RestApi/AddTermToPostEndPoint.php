<?php
	namespace Wprr\RestApi;
	
	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\RestApi\AddTermToPostEndPoint
	class AddTermToPostEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\AddTermToPostEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\AddTermToPostEndPoint::perform_call<br />");
			
			$post_id = $data['post_id'];
			$taxonomy = $data['taxonomy'];
			$id = intVal($data['id']);
			
			$return_object = array();
			
			$result = wp_set_post_terms($post_id, array($id), $taxonomy, true);
			//METODO: error checking
			
			return $this->output_success($return_object);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\AddTermToPostEndPoint<br />");
		}
	}
?>