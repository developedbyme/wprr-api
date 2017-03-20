<?php
	namespace MRouterData\RestApi;

	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;

	// \MRouterData\RestApi\SetMetadataEndpoint
	class SetMetadataEndpoint extends EndPoint {

		function __construct() {
			// echo("\OddCore\RestApi\SetMetadataEndpoint::__construct<br />");
		}

		public function perform_call($data) {
			// echo("\OddCore\RestApi\SetMetadataEndpoint::perform_call<br />");

			// TODO Add security. nouance.

			$post_id = sanitize_key($data['post_id']);
      $meta_key = sanitize_key($data['meta_key']);
      $meta_value = sanitize_key($data['meta_value']);
      $prev_value = sanitize_key($data['prev_value']);

			$meta_data = update_post_meta($post_id, $meta_key, $meta_value, $prev_value);

			return $this->output_success($meta_data);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\SetMetadataEndpoint<br />");
		}
	}
