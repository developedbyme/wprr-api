<?php
	namespace Wprr\RestApi;

	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;

	// \Wprr\RestApi\EditPostEndPoint
	class EditPostEndPoint extends EndPoint {

		function __construct() {
			// echo("\OddCore\RestApi\EditPostEndPoint::__construct<br />");
			
			parent::__construct();
		}

		public function perform_call($data) {
			// echo("\OddCore\RestApi\EditPostEndPoint::perform_call<br />");

			// TODO Add security. nouance.
			
			$id = $data['id'];
			$post_type = $data['post_type'];
			
			$args = array(
				'ID' => sanitize_key($id),
				'post_type' => sanitize_text_field($post_type)
			);
			
			if(isset($data['post_title'])) {
				$args['post_title'] = sanitize_text_field($data['post_title']);
			}
			if(isset($data['post_excerpt'])) {
				$args['post_excerpt'] = $data['post_excerpt'];
			}
			if(isset($data['post_content'])) {
				$args['post_content'] = $data['post_content'];
			}
			if(isset($data['post_status'])) {
				$args['post_status'] = sanitize_text_field($data['post_status']);
			}

			$result_id = wp_update_post($args);
			
			if($result_id) {
				if(isset($data['acf'])) {
					foreach($data['acf'] as $field_name => $value) {
						update_field($field_name, $value, $result_id);
					}
				}
			}

			return $this->output_success($result_id);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\EditPostEndPoint<br />");
		}
	}
