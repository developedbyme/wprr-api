<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	class Page {

		function __construct() {
			
		}
		
		public function encode($id) {
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$wprr_data_api->range()->encode_object_as($id, 'postTitle');
			$wprr_data_api->range()->encode_object_as($id, 'postContent');
			$wprr_data_api->range()->encode_object_as($id, 'postExcerpt');
			$wprr_data_api->range()->encode_object_as($id, 'postTerms');
			$wprr_data_api->range()->encode_object_as($id, 'permalink');
			$wprr_data_api->range()->encode_object_as($id, 'featuredImage');
			$wprr_data_api->range()->encode_object_as($id, 'postStatus');
			$wprr_data_api->range()->encode_object_as($id, 'postType');
			
			if(defined('DBM_CONTENT_VERSION')) {
				$wprr_data_api->range()->encode_object_as($id, 'pageSettings');
				$wprr_data_api->range()->encode_object_as($id, 'pageDataSources');
			}
		}
	}
?>