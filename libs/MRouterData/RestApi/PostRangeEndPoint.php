<?php
	namespace MRouterData\RestApi;
	
	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;
	
	// \MRouterData\RestApi\PostRangeEndPoint
	class PostRangeEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\PostRangeEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\PostRangeEndPoint::perform_call<br />");
			
			$post_type = $data['post_type'];
			
			$query_args = array(
				'post_type' => $post_type,
				'posts_per_page' => -1,
				'fields' => 'ids'
			);
			if(isset($data['taxonomy'])) {
				
				if(isset($data['terms'])) {
					$terms = explode(',', $data['terms']);
				}
				else {
					$terms = array($data['term']);
				}
				
				$tax_query = array(
					'taxonomy' => $data['taxonomy'],
					'field' => 'slug',
					'terms' => $terms
				);
				
				$query_args['tax_query'] = array();
				$query_args['tax_query'][] = $tax_query;
			}
			
			$posts = get_posts($query_args);
			
			$post_links = array();
			$encoder = new \MRouterData\MRouterDataEncoder();
			
			foreach($posts as $post_id) {
				$post_links[] = $encoder->encode_post_link($post_id);
			};
			
			return $this->output_success($post_links);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\PostRangeEndPoint<br />");
		}
	}
?>