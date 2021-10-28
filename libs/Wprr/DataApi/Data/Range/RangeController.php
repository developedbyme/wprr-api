<?php
	namespace Wprr\DataApi\Data\Range;

	// \Wprr\DataApi\Data\Range\RangeController
	class RangeController {
		
		protected $_selections = array();
		protected $_encoding = array();
		protected $_encoded_data = null;

		function __construct() {
			
		}
		
		public function register_selection($type, $file, $class) {
			if(!isset($this->_selections[$type])) {
				$this->_selections[$type] = array();
			}
			
			$select_registration = new \Wprr\DataApi\Data\Range\SelectRegistration();
			$select_registration->setup($file, $class);
			
			$this->_selections[$type][] = $select_registration;
			
			return $this;
		}
		
		public function register_encoding($type, $file, $class) {
			if(!isset($this->_encoding[$type])) {
				$this->_encoding[$type] = array();
			}
			
			$registration = new \Wprr\DataApi\Data\Range\EncodingRegistration();
			$registration->setup($file, $class);
			
			$this->_encoding[$type][] = $registration;
			
			return $this;
		}
		
		public function get_encoded_data() {
			if(!$this->_encoded_data) {
				$this->_encoded_data = new \Wprr\DataApi\Data\Range\EncodedData\EncodedData();
			}
			
			return $this->_encoded_data;
		}
		
		public function select($selections, $data) {
			//METODO
			$query = new \Wprr\DataApi\Data\Range\SelectQuery();
			
			$types = explode(',', $selections);
			foreach($types as $type) {
				if(!isset($this->_selections[$type])) {
					//METODO: throw
				}
			
				$selections = $this->_selections[$type];
				foreach($selections as $selection) {
					$selection->select($query, $data);
				}
			}
			
			$ids = $query->get_ids();
			
			foreach($types as $type) {
				$selections = $this->_selections[$type];
				foreach($selections as $selection) {
					$ids = $selection->filter($ids, $data);
				}
			}
			
			return $ids;
		}
		
		public function encode_range($ids, $encodings, $data) {
			
			$types = explode(',', $encodings);
			foreach($types as $type) {
				foreach($ids as $id) {
					$this->encode_object_as($id, $type);
				}
			}
			
			$encoded_data = $this->get_encoded_data();
			$return_data = $encoded_data->get_result();
			$return_data['mainRange'] = $ids;
			
			return $return_data; 
		}
		
		public function encode_object_as($id, $encoding_type) {
			if(!isset($this->_encoding[$encoding_type])) {
				//METODO: throw
			}
			
			$encoded_data = $this->get_encoded_data();
			
			if(!$encoded_data->has_encoded_object($id, $encoding_type)) {
				$encoded_data->add_object_to_range($id, $encoding_type);
				foreach($this->_encoding[$encoding_type] as $encoding) {
					$encoding->encode($id);
				}
			}
			
			return $id;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\RangeController<br />");
		}
	}
?>