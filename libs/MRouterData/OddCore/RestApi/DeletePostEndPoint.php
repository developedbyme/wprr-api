<?php
	namespace Wprr\OddCore\RestApi;
	
	use \WP_Query;
	use Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\OddCore\RestApi\DeletePostEndPoint
	class DeletePostEndPoint extends EndPoint {
		
		protected $_arguments = array();
		
		function __construct() {
			//echo("\OddCore\RestApi\DeletePostEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\DeletePostEndPoint::perform_call<br />");
			
			$post_type = $data['postType'];
			$id = intval($data['id']);
			
			remove_all_actions('transition_post_status');
			remove_all_actions('wp_trash_post');
			remove_all_actions('trashed_post');
			remove_all_actions('trash_post_comments');
			remove_all_actions('trashed_post_comments');
			
			remove_all_actions('pre_post_update');
			remove_all_actions('edit_attachment');
			remove_all_actions('attachment_updated');
			remove_all_actions('add_attachment');
			remove_all_actions('edit_post');
			remove_all_actions('post_updated');
			remove_all_actions("save_post_{$post_type}");
			remove_all_actions('save_post');
			remove_all_actions('wp_insert_post');
			
			$result = wp_trash_post($id);
			if(!$result) {
				return $this->output_error("Post not deleted");
			}
			
			return $this->output_success(null);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\DeletePostEndPoint<br />");
		}
	}
?>