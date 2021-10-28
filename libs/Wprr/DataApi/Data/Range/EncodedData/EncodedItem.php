<?php
	namespace Wprr\DataApi\Data\Range\EncodedData;

	// \Wprr\DataApi\Data\Range\EncodedData\EncodedItem
	class EncodedItem {
		
		public $data = array();
		
		function __construct() {
			
		}
		
		public function setup($id) {
			$this->data['id'] = $id;
			
			return $this;
		}
		
		public static function create($id) {
			$new_encoded_item = new EncodedItem();
			$new_encoded_item->setup($id);
			
			return $new_encoded_item;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\EncodedItem<br />");
		}
	}
?>