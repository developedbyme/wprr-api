<?php
	namespace Wprr\DataApi\Data\Range\EncodedData;

	// \Wprr\DataApi\Data\Range\EncodedData\EncodedData
	class EncodedData {
		
		protected $_encoded_items = array();
		protected $_encoded_ranges = array();
		
		function __construct() {
			
		}
		
		public function reset() {
			//METODO
		}
		
		public function &get_encoded_range($type) {
			if(!isset($this->_encoded_ranges[$type])) {
				$this->_encoded_ranges[$type] = array();
			}
			
			return $this->_encoded_ranges[$type];
		}
		
		public function has_encoded_object($id, $type) {
			$range = &$this->get_encoded_range($type);
			
			return in_array($id, $range);
		}
		
		public function add_object_to_range($id, $type) {
			$range = &$this->get_encoded_range($type);
			
			$range[] = $id;
			$this->prepare_item($id);
		}
		
		public function prepare_item($id) {
			if(!isset($this->_encoded_items[$id])) {
				$this->_encoded_items[$id] = \Wprr\DataApi\Data\Range\EncodedData\EncodedItem::create($id);
			}
			
			return $this->_encoded_items[$id];
		}
		
		public function get_item($id) {
			$this->prepare_item($id);
			return $this->_encoded_items[$id];
		}
		
		public function get_result() {
			$return_data = array();
			$return_data['encodings'] = $this->_encoded_ranges;
			$return_data['items'] = array_map(function($item) {return $item->data;}, $this->_encoded_items);
			
			return $return_data;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\RangeController<br />");
		}
	}
?>