<?php
	namespace Wprr\RestApi;
	
	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\RestApi\GetTaxonomiesEndPoint
	class GetTermsEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\GetTermsEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\GetTermsEndPoint::perform_call<br />");
			
			$return_array = array();
			
			do_action(M_ROUTER_DATA_DOMAIN.'/prepare_api_request', $data);
			
			$taxonomy = $data['taxonomy'];
			
			$terms = get_terms(array(
				'taxonomy' => $taxonomy,
				'hide_empty' => false
			));
			
			$encoder = new \Wprr\WprrEncoder();
			
			foreach($terms as $term) {
				$return_array[] = $encoder->encode_term_link($term);
			};
			
			return $this->output_success($return_array);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\GetTermsEndPoint<br />");
		}
	}
?>