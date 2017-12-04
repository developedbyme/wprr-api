<?php
	namespace MRouterData\RestApi;
	
	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;
	
	// \MRouterData\RestApi\GetTaxonomiesEndPoint
	class GetTaxonomiesEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\GetTaxonomiesEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\GetTaxonomiesEndPoint::perform_call<br />");
			
			$return_array = array();
  
			if(isset($data['language'])) {
				global $sitepress;
	
				if(isset($sitepress)) {
					$sitepress->switch_lang($data['language']);
				}
			}
			
			$taxonomies = get_taxonomies();
			
			foreach($taxonomies as $taxonomy) {
				$current_taxonomy_data = get_taxonomy($taxonomy);
				
				$encoded_data = array();
				$encoded_data['name'] = $current_taxonomy_data->name;
				$encoded_data['label'] = $current_taxonomy_data->label;
				$encoded_data['singularLabel'] = $current_taxonomy_data->labels->singular_name;
				$encoded_data['postTypes'] = $current_taxonomy_data->object_type;
				
				$return_array[] = $encoded_data;
			}
			
			return $this->output_success($return_array);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\GetTaxonomiesEndPoint<br />");
		}
	}
?>