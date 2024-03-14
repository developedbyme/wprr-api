<?php
	namespace Wprr\DataApi\WordPress;

	// \Wprr\DataApi\WordPress\WordPress
	class WordPress {
		
		protected $_taxonomies = array();
		protected $_posts = array();
		protected $_users = array();
		protected $_fields_structures = array();
		protected $_trusted_roles = array();
		protected $_woocommerce = null;
		protected $_editor = null;
		protected $_front_page_id = 0;

		function __construct() {
			$this->add_trusted_role('administrator');
		}
		
		public function add_trusted_role($role) {
			$this->_trusted_roles[] = $role;
			
			return $this;
		}
		
		public function woocommerce() {
			if(!$this->_woocommerce) {
				$this->_woocommerce = new \Wprr\DataApi\WordPress\WooCommerce\WooCommerce();
			}
			
			return $this->_woocommerce;
		}
		
		public function editor() {
			if(!$this->_editor) {
				$this->_editor = new \Wprr\DataApi\WordPress\Editor\Editor();
			}
			
			return $this->_editor;
		}
		
		public function get_taxonomy($name) {
			if(!isset($this->_taxonomies[$name])) {
				$new_taxonomy = new \Wprr\DataApi\WordPress\Taxonomy();
				$new_taxonomy->setup($name);
				$this->_taxonomies[$name] = $new_taxonomy;
			}
			
			return $this->_taxonomies[$name];
		}
		
		public function get_taxonomy_term($path) {
			$temp_array = explode(':', $path);
			$taxonomy = $temp_array[0];
			$term_name = $temp_array[1];
			
			$term = $this->get_taxonomy($taxonomy)->get_term($term_name);
			
			return $term;
		}
		
		public function get_post($id) {
			if(!isset($this->_posts[$id])) {
				$new_post = new \Wprr\DataApi\WordPress\Post();
				$new_post->setup($id);
				$this->_posts[$id] = $new_post;
			}
			
			return $this->_posts[$id];
		}
		
		public function get_posts($ids) {
			$return_array = array();
			
			foreach($ids as $id) {
				$return_array[] = $this->get_post($id);
			}
			
			return $return_array;
		}
		
		public function load_meta_for_relations($ids) {
			$ids_to_load = array();
			
			foreach($ids as $id) {
				$post = $this->get_post($id);
				if(!$post->has_parsed_meta('startAt')) {
					$ids_to_load[] = (int)$id;
				}
			}
			
			if(!empty($ids_to_load)) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
			
				$query = 'SELECT post_id as id, meta_key, meta_value FROM '.DB_TABLE_PREFIX.'postmeta WHERE meta_key IN ("startAt", "endAt", "fromId", "toId") AND post_id IN ('.implode(',', $ids_to_load).')';
				$meta_fields = $db->query_without_storage($query);
			
				foreach($meta_fields as $meta_field) {
					$id = (int)$meta_field['id'];
					
					$this->get_post($id)->set_parsed_meta($meta_field['meta_key'], (int)$meta_field['meta_value']);
				}
			}
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
			
				$query = 'SELECT post_id as id, meta_key, meta_value FROM '.DB_TABLE_PREFIX.'postmeta WHERE post_id IN ('.implode(',', $ids_to_load).')';
				$meta_fields = $db->query_without_storage($query);
			
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
				
				$query = 'SELECT '.DB_TABLE_PREFIX.'term_relationships.object_id as id, '.DB_TABLE_PREFIX.'term_relationships.term_taxonomy_id, '.DB_TABLE_PREFIX.'term_taxonomy.taxonomy FROM '.DB_TABLE_PREFIX.'term_relationships INNER JOIN '.DB_TABLE_PREFIX.'term_taxonomy WHERE '.DB_TABLE_PREFIX.'term_relationships.term_taxonomy_id = '.DB_TABLE_PREFIX.'term_taxonomy.term_taxonomy_id AND '.DB_TABLE_PREFIX.'term_relationships.object_id IN ('.implode(',', $ids_to_load).')';
				
				$rows = $db->query_without_storage($query);
			
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
		
		public function get_users($ids) {
			
			$return_array = array();
			
			foreach($ids as $id) {
				if(!isset($this->_users[$id])) {
					$new_user = new \Wprr\DataApi\WordPress\User();
					$new_user->setup($id);
					$this->_users[$id] = $new_user;
				}
				
				$return_array[] = $this->_users[$id];
			}
			
			return $return_array;
		}
		
		public function get_fields_structure($type) {
			if(!isset($this->_fields_structures[$type])) {
				$new_fields_structure = new \Wprr\DataApi\WordPress\Field\FieldsStructure();
				$new_fields_structure->setup($type);
				$this->_fields_structures[$type] = $new_fields_structure;
			}
			
			return $this->_fields_structures[$type];
		}
		
		public function get_language_by_path($path) {
			$current_language = null;
			
			if(defined('DEFAULT_LANGUAGE')) {
				$current_language = DEFAULT_LANGUAGE;
				
				if(defined('LANGUAGE_BASE_URLS')) {
					foreach(LANGUAGE_BASE_URLS as $language_url => $language_code) {
						if (substr($path, 0, strlen($language_url)) == $language_url) {
							$current_language = $language_code;
							break;
						}
					}
				}
			}
			
			return $current_language;
		}
		
		public function get_post_id_by_path($path) {
			
			$current_language = null;
			$default_language = null;
			if(defined('DEFAULT_LANGUAGE')) {
				$current_language = DEFAULT_LANGUAGE;
				$default_language = DEFAULT_LANGUAGE;
				if(defined('LANGUAGE_BASE_URLS')) {
					foreach(LANGUAGE_BASE_URLS as $language_url => $language_code) {
						if (substr($path, 0, strlen($language_url)) == $language_url) {
							$path = substr($path, strlen($language_url));
							$current_language = $language_code;
							break;
						}
					}
				}
			}
			
			if($path === '') {
				if($current_language && $current_language !== $default_language) {
					//METODO: get front page in language
				}
				
				return $this->get_front_page_id();
				
			}
			
			global $wprr_data_api;
			$db = $wprr_data_api->database();
			
			$ids_in_language = array();
			if($current_language) {
				$ids_in_language_result = $db->query_without_storage('SELECT element_id as id FROM '.DB_TABLE_PREFIX.'icl_translations WHERE language_code = \''.$db->escape($current_language).'\'');
				$ids_in_language = array_map(function($item) {return $item['id'];}, $ids_in_language_result);
			}
			
			$slugs = explode('/', $path);
			
			
			
			$current_id = 0;
			foreach($slugs as $slug) {
				$query = $db->new_select_query();
				$query->set_post_types(PUBLIC_POST_TYPES);
				$query->with_parent($current_id);
				if(!$current_id && $current_language) {
					$query->include_only($ids_in_language);
				}
				$query->with_slug($slug);
				$new_id = $query->get_id();
				$current_id = $new_id;
				if(!$current_id) {
					break;
				}
			}
			
			if(!$current_id) {
				if(isset(REWRITE_POST_TYPES[$slugs[0]])) {
					$current_post_type = REWRITE_POST_TYPES[$slugs[0]];
					array_shift($slugs);
					foreach($slugs as $slug) {
						$query = $db->new_select_query();
						$query->set_post_types(array($current_post_type));
						$query->with_parent($current_id);
						if(!$current_id && $current_language) {
							$query->include_only($ids_in_language);
						}
						$query->with_slug($slug);
						$new_id = $query->get_id();
						$current_id = $new_id;
						if(!$current_id) {
							break;
						}
					}
				}
			}
			
			return $current_id;
		}
		
		public function get_front_page_id() {
			
			if(!$this->_front_page_id) {
				global $wprr_data_api;
				$db = $wprr_data_api->database();
			
				$result = $db->query_first('SELECT option_value as id FROM '.DB_TABLE_PREFIX.'options WHERE option_name = "page_on_front"');
				if($result) {
					$this->_front_page_id = (int)$result['id'];
				}
			}
			
			return $this->_front_page_id;
		}
		
		public function is_user_trusted($user) {
			$roles = $user->get_roles();
			
			foreach($this->_trusted_roles as $trusted_role) {
				if(in_array($trusted_role, $roles)) {
					return true;
				}
			}
			
			return false;
		}
		
		public function object_relation_query_from_ids($ids, $path) {
			return \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationQuery::get_posts($this->get_posts($ids), $path);
		}
		
		public function get_global_object($identifier) {
			global $wprr_data_api;
			$post_id = $wprr_data_api->range()->new_query()->include_private()->include_term_by_path('dbm_type', 'global-item')->meta_query('identifier', $identifier)->get_id(); 
			
			if($post_id) {
				return $this->get_post($post_id);
			}
			
			return null;
		}
		
		public function get_linked_global_object($identifier) {
			$global_post = $this->get_global_object($identifier);
			if($global_post) {
				return $global_post->single_object_relation_query('out:pointing-to:*');
			}
			
			return null;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\WordPress<br />");
		}
	}
?>