<?php
	namespace MRouterData\RestApi;
	
	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;
	
	// \MRouterData\RestApi\RemoveTermFromPostEndPoint
	class RemoveTermFromPostEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\RemoveTermFromPostEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\RemoveTermFromPostEndPoint::perform_call<br />");
			
			$post_id = $data['post_id'];
			$taxonomy = $data['taxonomy'];
			$id = intVal($data['id']);
			
			$return_object = array();
			
			$ids = array();
			
			$current_terms = wp_get_post_terms($post_id, $taxonomy);
			foreach($current_terms as $current_term) {
				if($current_term->term_id != $id) {
					$ids[] = $current_term->term_id;
				}
			}
			
			$result = wp_set_post_terms($post_id, $ids, $taxonomy, false);
			//METODO: error checking
			
			return $this->output_success($return_object);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\RemoveTermFromPostEndPoint<br />");
		}
	}
?>