<?php
	namespace Wprr\OddCore\RestApi;
	
	use \WP_Query;
	use Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\OddCore\RestApi\CountPostsEndPoint
	class CountPostsEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\CountPostsEndPoint::__construct<br />");
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\CountPostsEndPoint::perform_call<br />");
			//echo($data['posttype']);
			
			$count_data = wp_count_posts($data['postType']);
			
			if(!isset($count_data->publish) || !isset($count_data->draft)) {
				return $this->output_error("No post type");
			}
			
			return $this->output_success(array("publish" => intval($count_data->publish), "draft" => intval($count_data->draft)));
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\CountPostsEndPoint<br />");
		}
	}
?>