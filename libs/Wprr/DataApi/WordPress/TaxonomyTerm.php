<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\TaxonomyTerm
	class TaxonomyTerm implements \JsonSerializable {
		
		protected $_path = null;
		protected $_data = null;
		protected $_taxonomy = null;
		protected $_ids = null;
		
		function __construct() {
			
		}
		
		public function setup($path, $data, $taxonomy) {
			$this->_path = $path;
			$this->_data = $data;
			$this->_taxonomy = $taxonomy;
			
			return $this;
		}
		
		public function get_id() {
			return $this->_data['id'];
		}
		
		public function get_path() {
			return $this->_path;
		}
		
		public function get_identifier() {
			return $this->_taxonomy->get_name().':'.$this->get_path();
		}
		
		public function get_slug() {
			return $this->_data['slug'];
		}
		
		public function get_name() {
			return $this->_data['name'];
		}
		
		public function get_taxonomy() {
			return $this->_taxonomy;
		}
		
		public function get_parent() {
			$parent_id = $this->_data['parent'];
			if($parent_id) {
				return $this->get_taxonomy()->get_term_by_id($parent_id);
			}
			
			return null;
		}
		
		public function is_descendant_of($term) {
			$current_term = $this->get_parent();
			
			while($current_term) {
				if($current_term === $term) {
					return true;
				}
				
				$current_term = $current_term->get_parent();
			}
			
			return false;
		}
		
		public function get_ids() {
			
			if($this->_ids === null) {
				global $wprr_data_api;
				
				$wprr_data_api->performance()->start_meassure('Database::get_ids get');
				
				$db = $wprr_data_api->database();
				
				$query = 'SELECT object_id as id FROM '.DB_TABLE_PREFIX.'term_relationships WHERE term_taxonomy_id = "'.$this->_data['id'].'"';
				$posts = $db->query_without_storage($query);
				
				$this->_ids = array_map(function($item) {
					return (int)$item['id'];
				}, $posts);
				
				$wprr_data_api->performance()->stop_meassure('Database::get_ids get');
			}
			
			return $this->_ids;
		}
		
		public function invalidate() {
			$this->_ids = null;
			
			return $this;
		}
		
		public function __toString() {
			return "[TaxonomyTerm id=".$this->get_id()." slug=".$this->get_slug()."]";
		}
		
		public function jsonSerialize():mixed {
			return $this->get_identifier();
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\TaxonomyTerm<br />");
		}
	}
?>