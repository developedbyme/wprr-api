<?php
	namespace Wprr\RestApi;

	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;

	// \Wprr\RestApi\CustomRangeEndPoint
	class CustomRangeEndPoint extends EndPoint {

		function __construct() {
			//echo("\OddCore\RestApi\CustomRangeEndPoint::__construct<br />");

			parent::__construct();
		}

		public function perform_call($data) {
			//echo("\OddCore\RestApi\CustomRangeEndPoint::perform_call<br />");

			$range_type = $data['range_type'];

			$has_permission_filter_name = M_ROUTER_DATA_DOMAIN.'/custom_range_has_permission_'.$range_type;
			$query_filter_name = M_ROUTER_DATA_DOMAIN.'/custom_range_query_'.$range_type;
			$encode_filter_name = M_ROUTER_DATA_DOMAIN.'/custom_range_encode_'.$range_type;


			if(!has_filter($query_filter_name)) {
				return $this->output_error('No range for type '.$range_type);
			}

			$has_permission = apply_filters($has_permission_filter_name, true, $data);
			if(!$has_permission) {
				return $this->output_error('Access denied');
			}
			
			do_action(M_ROUTER_DATA_DOMAIN.'/prepare_api_request', $data);

			$query_args = array(
				'post_type' => 'post',
				'posts_per_page' => -1,
				'fields' => 'ids',
				'suppress_filters' => 0
			);

			if(isset($data['order'])) {
				$query_args['order'] = $data['order'];
			}
			if(isset($data['orderby'])) {
				$query_args['orderby'] = $data['orderby'];
			}

			$query_args = apply_filters($query_filter_name, $query_args, $data);

			$posts = get_posts($query_args);

			$post_links = array();

			foreach($posts as $post_id) {
				$post_links[] = apply_filters($encode_filter_name, mrouter_encode_post_link($post_id), $post_id, $data);
			};

			return $this->output_success($post_links);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\CustomRangeEndPoint<br />");
		}
	}
?>
