<?php
	namespace MRouterData\RestApi;

	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;

	// \MRouterData\RestApi\PostCaseEndPoint
	class PostCaseEndPoint extends EndPoint {

		function __construct() {
			//echo("\OddCore\RestApi\PostCaseEndPoint::__construct<br />");
		}

		public function perform_call($data) {
			//echo("\OddCore\RestApi\PostCaseEndPoint::perform_call<br />");

			$post_type = $data['post_type'];

			$query_args = array(
				'post_type' => ['post', 'expohouse_case'],
				'posts_per_page' => -1,
				'fields' => 'ids'
			);

			$posts = get_posts($query_args);

			foreach ($posts as $post_key => $post) {
				$categories = get_the_category($post);
				$post_type = get_post_type($post);

				foreach ($categories as $category) {
					if ($post_type === 'post' && $category->slug !== 'nyheter') {
						unset($posts[$post_key]);
					}
				}
			}

			$post_links = array();
			$encoder = new \MRouterData\MRouterDataEncoder();

			foreach($posts as $post_id) {
				$post_links[] = $encoder->encode_post_link($post_id);
			}

			return $this->output_success($post_links);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\PostCaseEndPoint<br />");
		}
	}
