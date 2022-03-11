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
		
		public function get_posts($ids) {
			$return_array = array();
			
			foreach($ids as $id) {
				$return_array[] = $this->get_post($id);
			}
			
			return $return_array;
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
				
				$query = 'SELECT wp_term_relationships.object_id as id, wp_term_relationships.term_taxonomy_id, wp_term_taxonomy.taxonomy FROM wp_term_relationships INNER JOIN wp_term_taxonomy WHERE wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id AND wp_term_relationships.object_id IN ('.implode(',', $ids_to_load).')';
				
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
		
		public function get_post_id_by_path($path) {
			
			if($path === '') {
				return $this->get_front_page_id();
			}
			
			$slugs = explode('/', $path);
			
			global $wprr_data_api;
			$db = $wprr_data_api->database();
			
			$current_id = 0;
			foreach($slugs as $slug) {
				$query = $db->new_select_query();
				$query->set_post_types(PUBLIC_POST_TYPES);
				$query->with_parent($current_id);
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
			global $wprr_data_api;
			$db = $wprr_data_api->database();
			
			$result = $db->query_first('SELECT option_value as id FROM wp_options WHERE option_name = "page_on_front"');
			if($result) {
				return (int)$result['id'];
			}
			
			return 0;
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

		public static function test_import() {
			echo("Imported \Wprr\DataApi\WordPress<br />");
		}
	}
?>