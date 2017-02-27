<?php
	namespace MRouterData\RestApi;
	
	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;
	
	// \MRouterData\RestApi\ImageRangeEndPoint
	class ImageRangeEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\ImageRangeEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\ImageRangeEndPoint::perform_call<br />");
			
			$post_type = $data['post_type'];
			
			$query_args = array(
				'post_type' => $post_type,
				'posts_per_page' => -1,
				'fields' => 'ids'
			);
			if(isset($data['taxonomy'])) {
				
				$tax_query = array(
					'taxonomy' => $data['taxonomy'],
					'field' => 'slug',
					'terms' => array($data['term'])
				);
				
				$query_args['tax_query'] = array();
				$query_args['tax_query'][] = $tax_query;
			}
			
			$posts = get_posts($query_args);
			
			$post_links = array();
			$encoder = new \MRouterData\MRouterDataEncoder();
			
			foreach($posts as $post_id) {
				$post_links[] = $encoder->encode_image(get_post($post_id));
			};
			
			return $this->output_success($post_links);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\ImageRangeEndPoint<br />");
		}
	}
?>