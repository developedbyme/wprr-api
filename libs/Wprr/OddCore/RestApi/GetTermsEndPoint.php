<?php
	namespace Wprr\OddCore\RestApi;
	
	use \WP_Query;
	use Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\OddCore\RestApi\GetTermsEndPoint
	class GetTermsEndPoint extends EndPoint {
		
		protected $_arguments = array();
		
		function __construct() {
			//echo("\OddCore\RestApi\GetTermsEndPoint::__construct<br />");
			
			$this->set_arguments(array("post_type" => "post", "post_status" => "publish"));
		}
		
		public function set_arguments($arguments) {
			
			foreach($arguments as $key => $value) {
				$this->_arguments[$key] = $value;
			}
			
			return $this;
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\GetTermsEndPoint::perform_call<br />");
			
			$taxonomy = $data["taxonomy"];
			
			$return_array = array();
			
			$terms = get_terms($taxonomy, array(
				'hide_empty' => 0,
			));
			
			if(!empty($terms) && !is_wp_error($terms)) {
				
				foreach($terms as $term) {
					$current_data = array('id' => $term->term_id, 'name' => $term->name, 'slug' => $term->slug);
					$meta = get_term_meta($term->term_id);
					$current_data['meta'] = empty($meta) ? NULL : $meta;
					
					$return_array[] = $current_data;
				}
			}
			else {
				//METODO: erro handling
			}
			
			return $this->output_success($return_array);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\GetTermsEndPoint<br />");
		}
	}
?>