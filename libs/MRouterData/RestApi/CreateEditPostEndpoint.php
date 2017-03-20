<?php
	namespace MRouterData\RestApi;

	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;

	// \MRouterData\RestApi\CreateEditPostEndpoint
	class CreateEditPostEndpoint extends EndPoint {

		function __construct() {
			// echo("\OddCore\RestApi\CreateEditPostEndpoint::__construct<br />");
		}

		public function perform_call($data) {
			// echo("\OddCore\RestApi\CreateEditPostEndpoint::perform_call<br />");

			// TODO Add security. nouance.

      $attributes = array(
        'ID' => sanitize_key($data['ID']),
        'post_author' => 0,
        'post_content' => sanitize_text_field($data['post_content']),
        'post_title' => sanitize_text_field($data['post_title']),
        'post_excerpt' => sanitize_text_field($data['post_excerpt']),
        'post_status' => sanitize_text_field($data['post_status']),
        'post_type' => sanitize_text_field($data['post_type']),
        'comment_status' => sanitize_text_field($data['comment_status']),
        'post_password' => sanitize_text_field($data['post_password']),
        'post_parent' => sanitize_key($data['post_parent'])
      );

			$insert_post = wp_insert_post($attributes);

			return $this->output_success($insert_post);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\CreateEditPostEndpoint<br />");
		}
	}
