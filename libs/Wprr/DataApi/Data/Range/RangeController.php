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
			
			global $wprr_data_api;
			
			$query = new \Wprr\DataApi\Data\Range\SelectQuery();
			
			$types = explode(',', $selections);
			foreach($types as $type) {
				if(!isset($this->_selections[$type])) {
					throw(new \Exception('Select '.$type.' doesn\'t exist'));
				}
				
				$wprr_data_api->performance()->start_meassure('RangeController::select select '.$type);
			
				$selections = $this->_selections[$type];
				foreach($selections as $selection) {
					$selection->select($query, $data);
				}
				
				$wprr_data_api->performance()->stop_meassure('RangeController::select select '.$type);
			}
			
			$ids = $query->get_ids();
			
			foreach($types as $type) {
				
				$wprr_data_api->performance()->start_meassure('RangeController::select filter '.$type);
				
				$selections = $this->_selections[$type];
				foreach($selections as $selection) {
					$ids = $selection->filter($ids, $data);
				}
				
				$wprr_data_api->performance()->stop_meassure('RangeController::select filter '.$type);
			}
			
			return $ids;
		}
		
		public function encode_range($ids, $encodings, $data) {
			
			global $wprr_data_api;
			
			$types = explode(',', $encodings);
			foreach($types as $type) {
				$wprr_data_api->performance()->start_meassure('RangeController::encode_range '.$type);
				foreach($ids as $id) {
					$this->encode_object_as($id, $type);
				}
				$wprr_data_api->performance()->stop_meassure('RangeController::encode_range '.$type);
			}
			
			$encoded_data = $this->get_encoded_data();
			$return_data = $encoded_data->get_result();
			$return_data['ids'] = $ids;
			
			return $return_data; 
		}
		
		public function encode_objects_as($ids, $encoding_type) {
			foreach($ids as $id) {
				$this->encode_object_as($id, $encoding_type);
			}
			
			return $ids;
		}
		
		public function encode_object_as($id, $encoding_type) {
			//var_dump("encode_object_as", $id);
			
			global $wprr_data_api;
			
			if(!isset($this->_encoding[$encoding_type])) {
				throw(new \Exception('Encode '.$encoding_type.' doesn\'t exist'));
			}
			
			if(!$id) {
				return $id;
			}
			
			$wprr_data_api->performance()->start_meassure('RangeController::encode_object_as '.$encoding_type);
			
			$encoded_data = $this->get_encoded_data();
			
			if(!$encoded_data->has_encoded_object($id, $encoding_type)) {
				$encoded_data->add_object_to_range($id, $encoding_type);
				foreach($this->_encoding[$encoding_type] as $encoding) {
					$encoding->encode($id);
				}
			}
			
			$wprr_data_api->performance()->stop_meassure('RangeController::encode_object_as '.$encoding_type);
			
			return $id;
		}
		
		public function get_encoded_object($id) {
			$encoded_data = $this->get_encoded_data();
			
			return $encoded_data->get_item($id);
		}
		
		public function encode_term($term) {
			
			if(!$term) {
				return null;
			}
			
			$encoded_data = $this->get_encoded_data();
			
			$id = $term->get_identifier();
			
			$encoded_item = $encoded_data->get_item($id);
			
			if(!$encoded_data->has_encoded_object($id, 'taxonomyTerm')) {
				$encoded_item->data['id'] = $term->get_id();
				$encoded_item->data['slug'] = $term->get_slug();
				$encoded_item->data['name'] = $term->get_name();
				$encoded_item->data['path'] = $term->get_path();
				$encoded_data->add_object_to_range($id, 'taxonomyTerm');
			}
			
			return $id;
		}
		
		public function encode_terms($terms) {
			$identifiers = array();
			foreach($terms as $term) {
				$identifiers[] = $this->encode_term($term);
			}
			
			return $identifiers;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\RangeController<br />");
		}
	}
?>