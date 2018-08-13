<?php
	namespace Wprr\RestApi\Admin;

	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;

	// \Wprr\RestApi\Admin\ChangePostEndpoint
	class ChangePostEndpoint extends EndPoint {

		function __construct() {
			// echo("\Wprr\RestApi\Admin\ChangePostEndpoint::__construct<br />");
		}

		public function perform_call($data) {
			// echo("\Wprr\RestApi\Admin\ChangePostEndpoint::perform_call<br />");

			//METODO Add security.
			
			do_action(WPRR_DOMAIN.'/prepare_api_request', $data);

			$post_id = $data['post_id'];
			
			$post = get_post($post_id);
			
			if($post) {
				//Check that we are allowed to change the post
				foreach($data['changes'] as $change) {
					$change_type = $change['type'];
					$change_data = $change['data'];
					
					//METODO: check if change is allowed
					do_action(WPRR_DOMAIN.'/admin/change_post/'.$change_type, $change_data, $post_id);
				}
			}
			else {
				//Log error
			}

			
			return $this->output_success(array('id' => $post_id));
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\CreateEditPostEndpoint<br />");
		}
	}
?>