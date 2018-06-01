<?php
	namespace Wprr\RestApi;
	
	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\RestApi\GetSiteDataEndPoint
	class GetSiteDataEndPoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\GetSiteDataEndPoint::__construct<br />");
			
			
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\GetSiteDataEndPoint::perform_call<br />");
			
			$return_array = array();
			
			$return_array['sitePath'] = get_site_url();
			$return_array['themePath'] = get_stylesheet_directory_uri();
			$return_array['restPath'] = rest_url();
			
			global $_wp_additional_image_sizes;

			$image_sizes = array();

			foreach ( get_intermediate_image_sizes() as $_size ) {
				if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
					$image_sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
					$image_sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
					$image_sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
				} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
					$image_sizes[ $_size ] = array(
						'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
						'height' => $_wp_additional_image_sizes[ $_size ]['height'],
						'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
					);
				}
			}
			$image_sizes['full'] = array(
				'width'  => 0,
				'height' => 0,
				'crop'   => false,
			);
			
			$return_array['imageSizes'] = $image_sizes;
			
			return $this->output_success($return_array);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\GetSiteDataEndPoint<br />");
		}
	}
?>