<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\FeaturedImage
	class FeaturedImage {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("FeaturedImage::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$image = $post->get_featured_image();
			if($image) {
				$encoded_data->data['image'] = $wprr_data_api->range()->encode_object_as($image->get_id(), 'image');
			}
			else {
				$encoded_data->data['image'] = null;
			}
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\FeaturedImage<br />");
		}
	}
?>