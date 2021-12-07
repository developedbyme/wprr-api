<?php
	namespace Wprr\DataApi\Data\Range;

	// \Wprr\DataApi\Data\Range\RangeController
	class RangeController {
		
		protected $_selections = array();
		protected $_encoding = array();
		protected $_encoded_data = null;
		protected $_queued_encodings = array();

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
				
				foreach($ids as $id) {
					$this->encode_object_as($id, $type);
				}
				
			}
			
			
			$debug_counter = 0;
			while(!empty($this->_queued_encodings)) {
				if($debug_counter++ > 10000) {
					//METODO: throw
					break;
				}
				
				$current_encoding = array_keys($this->_queued_encodings)[0];
				
				$wprr_data_api->performance()->start_meassure('RangeController::encode_range '.$current_encoding);
				
				$current_ids = $this->_queued_encodings[$current_encoding];
				unset($this->_queued_encodings[$current_encoding]);
				
				$this->_perform_encode_objects_as($current_ids, $current_encoding);
				
				$wprr_data_api->performance()->stop_meassure('RangeController::encode_range '.$current_encoding);
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
			
			$encoded_data = $this->get_encoded_data();
			
			if(!$encoded_data->has_encoded_object($id, $encoding_type)) {
				$encoded_data->add_object_to_range($id, $encoding_type);
				$this->_queued_encodings[$encoding_type][] = $id;
				//$this->_perform_encode_object_as($id, $encoding_type);
			}
			
			return $id;
		}
		
		protected function _perform_encode_object_as($id, $encoding_type) {
			
			global $wprr_data_api;
			
			$wprr_data_api->performance()->start_meassure('RangeController::_perform_encode_object_as '.$encoding_type);
			
			foreach($this->_encoding[$encoding_type] as $encoding) {
				$encoding->encode($id);
			}
			
			$wprr_data_api->performance()->stop_meassure('RangeController::_perform_encode_object_as '.$encoding_type);
			
			return $id;
		}
		
		protected function _perform_encode_objects_as($ids, $encoding_type) {
			
			foreach($this->_encoding[$encoding_type] as $encoding) {
				$encoding->prepare($ids);
			}
			
			foreach($ids as $id) {
				$this->_perform_encode_object_as($id, $encoding_type);
			}
			
			return $ids;
		}
		
		public function encode_object_if_encoding_exists_as($id, $encoding_type) {
			
			if(isset($this->_encoding[$encoding_type])) {
				$this->encode_object_as($id, $encoding_type);
			}
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
		
		public function encode_user($user) {
			
			$id = $user->get_id();
			$store_id = 'user'.$id;
			
			$encoded_data = $this->get_encoded_data();
			$encoded_item = $encoded_data->get_item($store_id);
			
			if(!$encoded_data->has_encoded_object($store_id, 'user')) {
				$encoded_item->data['id'] = $id;
				$encoded_item->data['name'] = $user->get_display_name();
				$encoded_item->data['gravatarHash'] = $user->get_gravatar_hash();
				$encoded_data->add_object_to_range($store_id, 'user');
			}
			
			return $store_id;
		}
		
		public function encode_term_and_parents($term) {
			if(!$term) {
				return null;
			}
			
			$id = $term->get_identifier();
			
			$current_term = $term;
			
			$debug_counter = 0;
			while($current_term) {
				if($debug_counter++ > 255) {
					throw(new \Exception('To many parents for term'));
				}
				$this->encode_term($current_term);
				$current_term = $current_term->get_parent();
			}
			
			return $id;
		}
		
		public function encode_terms($terms) {
			$identifiers = array();
			foreach($terms as $term) {
				$encoded_term_id = $this->encode_term($term);
				if($encoded_term_id) {
					$identifiers[] = $encoded_term_id;
				}
			}
			
			return $identifiers;
		}
		
		public function encode_taxonomy($taxonomy) {
			
			if(!$taxonomy) {
				return null;
			}
			
			$terms = $taxonomy->get_terms();
			$ids = $this->encode_terms($terms);
			
			$encoded_data = $this->get_encoded_data();
			
			$id = $taxonomy->get_identifier();
			
			$encoded_item = $encoded_data->get_item($id);
			
			if(!$encoded_data->has_encoded_object($id, 'taxonomy')) {
				$encoded_item->data['terms'] = $ids;
				$encoded_data->add_object_to_range($id, 'taxonomy');
			}
			
			$return_data = $encoded_data->get_result();
			$return_data['ids'] = $taxonomy->get_identifier();
			
			return $id; 
		}
		
		public function encode_fields_structure($fields_structure) {
			if(!$fields_structure) {
				return null;
			}
			
			global $wprr_data_api;
			
			$encoded_data = $this->get_encoded_data();
			
			$id = $fields_structure->get_identifier();
			
			$encoded_item = $encoded_data->get_item($id);
			
			if(!$encoded_data->has_encoded_object($id, 'fieldsStructure')) {
				
				$for_type = $fields_structure->get_type();
				
				if($for_type) {
					$encoded_item->data['forType'] = $this->encode_term($wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term($for_type));
				}
				else {
					$encoded_item->data['forType'] = null;
				}
				
				$fields = $fields_structure->get_fields();
				
				$encoded_fields = array();
				
				foreach($fields as $key => $field) {
					$encoded_fields[$key] = $this->encode_object_as($field->get_id(), 'fieldTemplate');
				}
				
				$encoded_item->data['fields'] = $encoded_fields;
				
				$encoded_data->add_object_to_range($id, 'fieldsStructure');
			}
			
			return $id;
		}
		
		public function encode_fields_structures($fields_structures) {
			$identifiers = array();
			foreach($fields_structures as $fields_structure) {
				$encoded_identifier = $this->encode_fields_structure($fields_structure);
				if($encoded_identifier) {
					$identifiers[] = $encoded_identifier;
				}
			}
			
			return $identifiers;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\RangeController<br />");
		}
	}
?>