<?php
	namespace MRouterData\OddCore\AjaxApi;
	
	use \WP_Query;
	use MRouterData\OddCore\AjaxApi\EndPoint as EndPoint;
	
	class CountPostsEndPoint extends EndPoint {
		
		protected $_post_type = "post";
		
		function __construct() {
			//echo("\OddCore\AjaxApi\CountPostsEndPoint::__construct<br />");
			
			
		}
		
		public function set_post_type($post_type) {
			$this->post_type = $post_type;
			
			return $this;
		}
		
		public function perform_call($data) {
			//echo("\OddCore\AjaxApi\CountPostsEndPoint::perform_call<br />");
			
			$count_data = wp_count_posts($this->post_type);
			
			$this->output_success(array("publish" => intval($count_data->publish), "draft" => intval($count_data->draft)));
		}
		
		public static function test_import() {
			echo("Imported \OddCore\AjaxApi\CountPostsEndPoint<br />");
		}
	}
?>