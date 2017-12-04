<?php
	namespace MRouterData\RestApi;
	
	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;
	
	// \MRouterData\RestApi\GetTaxonomiesEndPoint
	class GetTermsEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\GetTermsEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\GetTermsEndPoint::perform_call<br />");
			
			$return_array = array();
  
			if(isset($data['language'])) {
				global $sitepress;
	
				if(isset($sitepress)) {
					$sitepress->switch_lang($data['language']);
				}
			}
			
			$taxonomy = $data['taxonomy'];
			
			$terms = get_terms(array(
				'taxonomy' => $taxonomy,
				'hide_empty' => false
			));
			
			$encoder = new \MRouterData\MRouterDataEncoder();
			
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