<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\TaxonomyTerm
	class TaxonomyTerm {
		
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
		
		public function get_path() {
			return $this->_path;
		}
		
		public function get_identifier() {
			return $this->_taxonomy->get_name().':'.$this->get_path();
		}
		
		public function get_ids() {
			
			if($this->_ids === null) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$query = 'SELECT object_id as id FROM wp_term_relationships WHERE term_taxonomy_id = "'.$this->_data['id'].'"';
				$posts = $db->query($query);
				
				$this->_ids = array_map(function($item) {
					return (int)$item['id'];
				}, $posts);
			}
			
			return $this->_ids;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\TaxonomyTerm<br />");
		}
	}
?>