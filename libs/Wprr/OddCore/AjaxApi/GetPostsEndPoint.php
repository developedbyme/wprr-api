<?php
	namespace Wprr\OddCore\AjaxApi;
	
	use \WP_Query;
	use Wprr\OddCore\AjaxApi\EndPoint as EndPoint;
	
	class GetPostsEndPoint extends EndPoint {
		
		protected $_arguments = array();
		
		function __construct() {
			//echo("\OddCore\AjaxApi\GetPostsEndPoint::__construct<br />");
			
			$this->set_arguments(array("post_type" => "post", "post_status" => "publish"));
		}
		
		public function set_arguments($arguments) {
			
			foreach($arguments as $key => $value) {
				$this->_arguments[$key] = $value;
			}
			
			return $this;
		}
		
		public function perform_call($data) {
			//echo("\OddCore\AjaxApi\GetPostsEndPoint::perform_call<br />");
			
			$posts_per_page = intval($data["postsPerPage"]);
			$offset = intval($data["offset"]);
			
			$return_array = array();
			
			$this->set_arguments(array(
				"posts_per_page" => $posts_per_page,
				"offset" => $offset
			));
			$query = new WP_Query($this->_arguments);
			
			while ( $query->have_posts() ) {
				
				$query->the_post();
				
				$id = get_the_id();
				$thumbnail_id = get_post_thumbnail_id($id);
				$thumbnail_source = ($thumbnail_id) ? wp_get_attachment_image_src($thumbnail_id, 'single-post-thumbnail')[0] : NULL;
				
				$return_array[] = array(
					"id" => $id,
					"title" => get_the_title(),
					"thumbnailId" => $thumbnail_id,
					"thumbnailSource" => $thumbnail_source
				);
			}
			wp_reset_postdata();
			
			$this->output_success($return_array);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\AjaxApi\GetPostsEndPoint<br />");
		}
	}
?>