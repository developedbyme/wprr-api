<?php
	namespace Wprr\OddCore\RestApi;
	
	use \WP_Query;
	use Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\OddCore\RestApi\BatchTrashPostsEndPoint
	class BatchTrashPostsEndPoint extends EndPoint {
		
		protected $_arguments = array();
		
		function __construct() {
			//echo("\OddCore\RestApi\BatchTrashPostsEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\BatchTrashPostsEndPoint::perform_call<br />");
			
			$ids = explode(",", $data['ids']);
			
			$number_of_errors = 0;
			$number_of_posts_trashed = 0;
			
			remove_action( 'transition_post_status',           array( '\Wprr\Helper\Headlines', 'transition_post_status' ), 10, 3 );
			remove_action( 'transition_post_status',           array( '\Wprr\Helper\PartnerPromotion', 'transition_post_status' ), 10, 3 );
			
			foreach($ids as $id) {
				$result = wp_trash_post(intval($id));
				if($result) {
					$number_of_posts_trashed++;
				}
				else {
					$number_of_errors++;
				}
			}
			
			
			
			
			return $this->output_success(array("trashed" => $number_of_posts_trashed, "errors" => $number_of_errors));
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\BatchTrashPostsEndPoint<br />");
		}
	}
?>