<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\Image
	class Image {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("Image::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$metadata = $post->get_meta('_wp_attachment_metadata');
			
			$path_parts = explode("/", $metadata['file']);
			array_pop($path_parts);
			$folder = implode("/", $path_parts);
			
			$encoded_sizes = array();
			$encoded_sizes['full'] = array('url' => UPLOAD_URL.'/'.$metadata['file'], 'width' => $metadata['width'], 'height' => $metadata['height']);
			foreach($metadata['sizes'] as $name => $size_data) {
				$encoded_sizes[$name] = array('url' => UPLOAD_URL.'/'.$folder.'/'.$size_data['file'], 'width' => $size_data['width'], 'height' => $size_data['height']);
			}
			$encoded_data->data['sizes'] = $encoded_sizes;
			$encoded_data->data['alt'] = $post->get_meta('_wp_attachment_image_alt');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Image<br />");
		}
	}
?>