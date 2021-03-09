<?php
	namespace Wprr\RestApi;
	
	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\RestApi\GetTaxonomiesEndPoint
	class GetTermsEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\GetTermsEndPoint::__construct<br />");
			
			parent::__construct();
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\GetTermsEndPoint::perform_call<br />");
			
			$return_array = array();
			
			do_action(M_ROUTER_DATA_DOMAIN.'/prepare_api_request', $data);
			
			$taxonomy = $data['taxonomy'];
			
			$internal_terms = apply_filters('wprr/taxonomy/'.$taxonomy.'/internal_terms', array());
			
			$include_link = false;
			if(isset($data['includeLink'])) {
				$include_link = ($data['includeLink'] === "1");
			}
			
			$terms = get_terms(array(
				'taxonomy' => $taxonomy,
				'hide_empty' => false,
				'exclude_tree' => $internal_terms
			));
			
			$encoder = new \Wprr\WprrEncoder();
			
			foreach($terms as $term) {
				$return_array[] = $encoder->encode_term_link($term, $include_link);
			};
			
			return $this->output_success($return_array);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\GetTermsEndPoint<br />");
		}
	}
?>