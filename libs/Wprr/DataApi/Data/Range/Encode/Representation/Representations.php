<?php
	namespace Wprr\DataApi\Data\Range\Encode\Representation;

	// \Wprr\DataApi\Data\Range\Encode\Representation\Representations
	class Representations {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("Representations::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$related_items = $post->object_relation_query('in:of:representation');
			$related_ids = array_map(function($item) {return (int)$item->get_id();}, $related_items);
			
			$encoded_data->data['representations'] = $wprr_data_api->range()->encode_objects_as($related_ids, 'representation');
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Representations<br />");
		}
	}
?>