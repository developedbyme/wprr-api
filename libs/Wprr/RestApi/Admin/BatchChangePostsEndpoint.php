<?php
	namespace Wprr\RestApi\Admin;

	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;

	// \Wprr\RestApi\Admin\BatchChangePostsEndpoint
	class BatchChangePostsEndpoint extends EndPoint {
		
		protected $_return_data;
		
		function __construct() {
			// echo("\Wprr\RestApi\Admin\BatchChangePostsEndpoint::__construct<br />");
		}
		
		public function add_return_data($field, $data) {
			$this->_return_data[$field] = $data;
			
			return $this;
		}

		public function perform_call($data) {
			// echo("\Wprr\RestApi\Admin\BatchChangePostsEndpoint::perform_call<br />");

			//METODO Add security.
			
			do_action(WPRR_DOMAIN.'/prepare_api_request', $data);
			
			$ids_array = array();
			
			foreach($data['batch'] as $batch) {
				$post_id = $batch['id'];
				$ids_array[] = $post_id;
			
				$post = get_post($post_id);
			
				if($post) {
					wprr_apply_post_changes($post_id, $batch['changes'], $this);
				}
				else {
					//Log error
				}
			}
			
			//METODO: handle return data
			return $this->output_success(array('ids' => $ids_array));
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\CreateEditPostEndpoint<br />");
		}
	}
?>