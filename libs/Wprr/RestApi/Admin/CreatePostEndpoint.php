<?php
	namespace Wprr\RestApi\Admin;

	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;

	// \Wprr\RestApi\Admin\CreatePostEndpoint
	class CreatePostEndpoint extends EndPoint {
		
		protected $_return_data;
		
		function __construct() {
			// echo("\Wprr\RestApi\Admin\CreatePostEndpoint::__construct<br />");
			
			parent::__construct();
		}
		
		public function add_return_data($field, $data) {
			$this->_return_data[$field] = $data;
			
			return $this;
		}

		public function perform_call($data) {
			// echo("\Wprr\RestApi\Admin\CreatePostEndpoint::perform_call<br />");
			
			do_action(WPRR_DOMAIN.'/prepare_api_user', $data);
			do_action(WPRR_DOMAIN.'/prepare_api_request', $data);
			
			$this->_return_data = array();
			
			$post_type = sanitize_text_field($data['post_type']);
			
			$data_type = isset($data['dataType']) ? sanitize_text_field($data['dataType']) : "none";
			$creation_method = isset($data['creationMethod']) ? sanitize_text_field($data['creationMethod']) : "draft";
			
			$valid = apply_filters('wprr/admin/create_post/valid_combination', true, $post_type, $data_type, $creation_method, $data);
			if(!$valid) {
				return $this->output_error('Not a valid combination '.$post_type.' '.$data_type.' '.$creation_method);
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
			
			if(!has_filter('wprr/admin/create_post/insert/'.$creation_method)) {
				return $this->output_error('No creation type '.$creation_method);
			}
			
			try {
				$post_id = apply_filters('wprr/admin/create_post/insert/'.$creation_method, 0, $title, $post_type, $data_type, $data);
			}
			catch(\Exception $exception) {
				return $this->output_error($exception->getMessage());
			}
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
			
			$this->add_return_data('id', $post_id);
			$this->add_return_data('logs', $this->_logs);
			
			return $this->output_success($this->_return_data);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\CreateEditPostEndpoint<br />");
		}
	}
?>