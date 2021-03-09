<?php
	namespace Wprr\RestApi;

	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;

	// \Wprr\RestApi\SetMetadataEndpoint
	class SetMetadataEndpoint extends EndPoint {

		function __construct() {
			// echo("\OddCore\RestApi\SetMetadataEndpoint::__construct<br />");
			
			parent::__construct();
		}

		public function perform_call($data) {
			// echo("\OddCore\RestApi\SetMetadataEndpoint::perform_call<br />");

			// TODO Add security. nouance.

			$post_id = sanitize_key($data['post_id']);
      $meta_key = sanitize_key($data['meta_key']);
      $meta_value = sanitize_key($data['meta_value']);
      $prev_value = sanitize_key($data['prev_value']);

			$meta_data = update_post_meta($post_id, $meta_key, $meta_value, $prev_value);

			if(!$meta_data) {
				return $this->output_error("Metadata not edited/created");
			}

			return $this->output_success($meta_data);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\SetMetadataEndpoint<br />");
		}
	}
