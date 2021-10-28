<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\Taxonomy
	class Taxonomy {
		
		protected $_name = null;
		protected $_term_data = null;
		protected $_terms = array();

		function __construct() {
			
		}
		
		public function setup($name) {
			$this->_name = $name;
			
			return $this;
		}
		
		protected function get_term_data() {
			if(!$this->_term_data) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$terms_array = $db->query('SELECT term_taxonomy_id as id, term_id as termId, parent FROM wp_term_taxonomy WHERE taxonomy = "'.$db->escape($this->_name).'"');
				
				$terms = array();
				$term_map = array();
				$term_ids = array();
				foreach($terms_array as &$term) {
					
					$term['id'] = (int)$term['id'];
					$term['termId'] = (int)$term['termId'];
					$term['parent'] = (int)$term['parent'];
					
					$term_ids[] = $term['termId'];
					$term_map[$term['termId']] = $term['id'];
					$terms[$term['id']] = $term;
				}
				
				$term_names = $db->query('SELECT term_id as id, name, slug FROM wp_terms WHERE term_id IN ('.implode(',', $term_ids).')');
				
				foreach($term_names as $term_name) {
					$terms[$term_map[$term_name['id']]]['name'] = $term_name['name'];
					$terms[$term_map[$term_name['id']]]['slug'] = $term_name['slug'];
				}
				
				$this->_term_data = $terms;
			}
			
			return $this->_term_data;
		}
		
		protected function get_term_data_by_slug($slug, $parent) {
			$term_data = $this->get_term_data();
			
			foreach($term_data as $id => $term) {
				if($term['slug'] === $slug && $term['parent'] === $parent) {
					return $term;
				}
			}
			
			return null;
		}
		
		protected function prepare_term($path) {
			if(!isset($this->_terms[$path])) {
				
				$term_data = null;
				$parent = 0;
				$path_parts = explode('/', $path);
				foreach($path_parts as $path_part) {
					$term_data = $this->get_term_data_by_slug($path_part, $parent);
					if(!$term_data) {
						break;
					}
					$parent = $term_data['id'];
				}
				
				if($term_data) {
					$taxonomy_term = new \Wprr\DataApi\WordPress\TaxonomyTerm();
					$taxonomy_term->setup($path, $term_data, $this);
					$this->_terms[$path] = $taxonomy_term;
				}
				else {
					$this->_terms[$path] = null;
				}
			}
		}
		
		public function get_term($path) {
			$this->prepare_term($path);
			return $this->_terms[$path];
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Taxonomy<br />");
		}
	}
?>