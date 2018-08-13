<?php
	namespace Wprr\RestApi\Admin;

	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;

	// \Wprr\RestApi\Admin\CreatePostEndpoint
	class CreatePostEndpoint extends EndPoint {

		function __construct() {
			// echo("\Wprr\RestApi\Admin\CreatePostEndpoint::__construct<br />");
		}

		public function perform_call($data) {
			// echo("\Wprr\RestApi\Admin\CreatePostEndpoint::perform_call<br />");

			//METODO Add security.
			
			do_action(WPRR_DOMAIN.'/prepare_api_request', $data);
			
			$insert_arguments = array(
				'post_title' => sanitize_text_field($data['title']),
				'post_status' => 'draft',
				'post_type' => sanitize_text_field($data['post_type']),
			);

			$post_id = wp_insert_post($insert_arguments);
			
			if(isset($data['changes'])) {
				foreach($data['changes'] as $change) {
					$change_type = $change['type'];
					$change_data = $change['data'];
					
					//METODO: check if change is allowed
					do_action(WPRR_DOMAIN.'/admin/change_post/'.$change_type, $change_data, $post_id);
				}
			}
			
			return $this->output_success(array('id' => $post_id));
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\CreateEditPostEndpoint<br />");
		}
	}
?>