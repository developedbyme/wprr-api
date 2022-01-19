<?php
	namespace Wprr\DataApi\Data\Range\Encode;

	// \Wprr\DataApi\Data\Range\Encode\SequenceNumber
	class SequenceNumber {

		function __construct() {
			
		}
		
		public function encode($id) {
			//var_dump("SequenceNumber::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$encoded_data->data['number'] = $post->get_meta('number');
			$encoded_data->data['identifier'] = $post->get_meta('fullIdentifier');
			
			//METODO: link to number sequence
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\SequenceNumber<br />");
		}
	}
?>