<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\WordPress
	class WordPress {
		
		protected $_taxonomies = array();
		protected $_posts = array();
		protected $_users = array();
		protected $_fields_structures = array();

		function __construct() {
			
		}
		
		public function get_taxonomy($name) {
			if(!isset($this->_taxonomies[$name])) {
				$new_taxonomy = new \Wprr\DataApi\WordPress\Taxonomy();
				$new_taxonomy->setup($name);
				$this->_taxonomies[$name] = $new_taxonomy;
			}
			
			return $this->_taxonomies[$name];
		}
		
		public function get_post($id) {
			if(!isset($this->_posts[$id])) {
				$new_post = new \Wprr\DataApi\WordPress\Post();
				$new_post->setup($id);
				$this->_posts[$id] = $new_post;
			}
			
			return $this->_posts[$id];
		}
		
		public function load_meta_for_posts($ids) {
			$ids_to_load = array();
			
			foreach($ids as $id) {
				$post = $this->get_post($id);
				if(!$post->has_database_meta_data()) {
					$ids_to_load[] = (int)$id;
					$grouped_data = array();
					$grouped_data[$id] = array();
				}
			}
			
			if(!empty($ids_to_load)) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
			
				$query = 'SELECT post_id as id, meta_key, meta_value FROM wp_postmeta WHERE post_id IN ('.implode(',', $ids_to_load).')';
				$meta_fields = $db->query($query);
			
				foreach($meta_fields as $meta_field) {
					$id = (int)$meta_field['id'];
					$grouped_data[$id][] = $meta_field;
				}
			
				foreach($grouped_data as $id => $meta_array) {
					$this->get_post($id)->set_database_meta_data($meta_array);
				}
			}
		}
		
		public function load_taxonomy_terms_for_posts($ids) {
			//var_dump('load_taxonomy_terms_for_posts');
			//var_dump($ids);
			
			$ids_to_load = array();
			
			foreach($ids as $id) {
				$post = $this->get_post($id);
				if(!$post->has_database_taxonomy_terms()) {
					$ids_to_load[] = (int)$id;
					$grouped_data = array();
					$grouped_data[$id] = array();
				}
			}
			
			if(!empty($ids_to_load)) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
				
				$query = 'SELECT wp_term_relationships.object_id as id, wp_term_relationships.term_taxonomy_id, wp_term_taxonomy.taxonomy FROM wp_term_relationships INNER JOIN wp_term_taxonomy WHERE wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id AND wp_term_relationships.object_id = "'.$this->_id.'"';
				
				$rows = $db->query($query);
			
				foreach($rows as $row) {
					$id = (int)$row['id'];
					$grouped_data[$id][] = $row;
				}
			
				foreach($grouped_data as $id => $data) {
					$this->get_post($id)->set_database_taxonomy_terms($data);
				}
			}
		}
		
		public function get_user($id) {
			if(!isset($this->_users[$id])) {
				$new_user = new \Wprr\DataApi\WordPress\User();
				$new_user->setup($id);
				$this->_users[$id] = $new_user;
			}
			
			return $this->_users[$id];
		}
		
		public function get_fields_structure($type) {
			if(!isset($this->_fields_structures[$type])) {
				$new_fields_structure = new \Wprr\DataApi\WordPress\Field\FieldsStructure();
				$new_fields_structure->setup($type);
				$this->_fields_structures[$type] = $new_fields_structure;
			}
			
			return $this->_fields_structures[$type];
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\WordPress<br />");
		}
	}
?>