<?php
	namespace Wprr\RestApi\Admin;

	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;

	// \Wprr\RestApi\Admin\ChangePostEndpoint
	class ChangePostEndpoint extends EndPoint {
		
		protected $_return_data;
		
		function __construct() {
			// echo("\Wprr\RestApi\Admin\ChangePostEndpoint::__construct<br />");
			
			parent::__construct();
		}
		
		public function add_return_data($field, $data) {
			$this->_return_data[$field] = $data;
			
			return $this;
		}

		public function perform_call($data) {
			// echo("\Wprr\RestApi\Admin\ChangePostEndpoint::perform_call<br />");

			//METODO Add security.
			
			do_action(WPRR_DOMAIN.'/prepare_api_user', $data);
			do_action(WPRR_DOMAIN.'/prepare_api_request', $data);

			$post_id = $data['post_id'];
			
			$post = get_post($post_id);
			
			$this->_return_data = array('id' => $post_id);
			
			if($post) {
				if(isset($data['changes'])) {
					wprr_apply_post_changes($post_id, $data['changes'], $this);
				}
				else {
					$this->add_log('Request contains no changes.');
					return $this->output_error('Request contains no changes.');
				}
			}
			else {
				$this->add_log('No post with id '.$post_id);
				return $this->output_error('No post with id '.$post_id);
			}

			
			return $this->output_success($this->_return_data);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\CreateEditPostEndpoint<br />");
		}
	}
?>