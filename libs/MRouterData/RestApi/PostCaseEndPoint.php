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
			$post_cases = array();

			// Get the prio items from frontpage ACF
			$frontpage = get_option('page_on_front');
			if (get_field('frontpage_news_priority_output', $frontpage)) {
				$frontpage_news_prio = get_field('frontpage_news_priority_output', $frontpage);
				$frontpage_news_prio_ids = array_map(function($e) {
				    return is_object($e) ? $e->ID : $e['ID'];
				}, $frontpage_news_prio);

				$post_cases = array_merge($post_cases, $frontpage_news_prio_ids);
			} else {
				$frontpage_news_prio_ids = array();
			}

			// Get normal posts and cases
			$query_args = array(
				'post_type' => ['post', 'expohouse_case'],
				'posts_per_page' => -1,
				'fields' => 'ids'
			);

			$posts = get_posts($query_args);
			$posts = array_merge($post_cases, $posts);

			$posts = array_unique($posts);

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
