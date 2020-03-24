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
			
			do_action(WPRR_DOMAIN.'/prepare_api_request', $data);
			
			$post_type = sanitize_text_field($data['post_type']);
			
			$data_type = isset($data['dataType']) ? sanitize_text_field($data['dataType']) : "none";
			$creation_method = isset($data['creationMethod']) ? sanitize_text_field($data['creationMethod']) : "draft";
			
			$valid = apply_filters('wprr/admin/create_post/valid_combination', true, $post_type, $data_type, $creation_method, $data);
			if(!$valid) {
				return $this->output_error('Not a valid combination');
			}
			
			$is_allowed = apply_filters('wprr/admin/create_post/allow', current_user_can('edit_others_posts'), $data);
			$is_allowed = apply_filters('wprr/admin/create_post/allow/'.$post_type, $is_allowed, $data);
			$is_allowed = apply_filters('wprr/admin/create_post/allow/'.$post_type.'/'.$data_type, $is_allowed, $data);
			$is_allowed = apply_filters('wprr/admin/create_post/allow/'.$post_type.'/'.$data_type.'/'.$creation_method, $is_allowed, $data);
			$is_allowed = apply_filters('wprr/admin/create_post/override_allow', $is_allowed, $data);
			
			if(!$is_allowed) {
				return $this->output_error('Not permitted');
			}
			
			$title = sanitize_text_field($data['title']);
			
			$post_id = apply_filters('wprr/admin/create_post/insert/'.$creation_method, 0, $title, $post_type, $data_type, $data);
			if(!$post_id) {
				return $this->output_error('No post created');
			}
			if($data_type !== "none") {
				do_action(WPRR_DOMAIN.'/admin/create_post/apply_data_type', $post_id, $data_type, $data);
			}
			
			if(isset($data['changes'])) {
				try {
					wprr_apply_post_changes($post_id, $data['changes'], $this);
				}
				catch(\Exception $exception) {
					return $this->output_error($exception->getMessage());
				}
			}
			
			return $this->output_success(array('id' => $post_id, 'logs' => $this->_logs));
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\CreateEditPostEndpoint<br />");
		}
	}
?>