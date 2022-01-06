<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\PostTerms
	class PostTerms {

		function __construct() {
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_taxonomy_terms_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("PostTerms::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$terms = array();
			
			$taxonomy_names = $post->get_active_taxonomy_names();
			foreach($taxonomy_names as $taxonomy_name) {
				$terms[$taxonomy_name] = $wprr_data_api->range()->encode_terms($post->get_taxonomy_terms($taxonomy_name));
			}
			
			$encoded_data->data['terms'] = $terms;
			
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\PostTerms<br />");
		}
	}
?>