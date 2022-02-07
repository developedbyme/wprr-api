<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\RelatedProducts
	class RelatedProducts {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("RelatedProducts::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$ids = $post->get_meta('_crosssell_ids');
			
			if($ids) {
				$encoded_data->data['crosssellProducts'] = $wprr_data_api->range()->encode_objects_as($ids, 'product');
				$wprr_data_api->range()->encode_objects_as($ids, 'preview');
			}
			else {
				$encoded_data->data['crosssellProducts'] = array();
			}
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\RelatedProducts<br />");
		}
	}
?>