<?php
	namespace Wprr\OddCore\Utils;
	
	// \Wprr\OddCore\Utils\TaxonomyFunctions
	class TaxonomyFunctions {
		
		public static function get_term_by_slugs($slugs, $taxonomy) {
			
			$current_id = 0;
			
			foreach($slugs as $slug) {
				$args = array(
					'taxonomy' => $taxonomy,
					'fields' => 'ids',
					'slug' => $slug,
					'parent' => $current_id,
					'hide_empty' => false
				);
					
				$terms = get_terms($args);
				
				if(empty($terms)) {
					return null;
				}
				$current_id = $terms[0];
			}
			
			return get_term_by('id', $current_id, $taxonomy);
		}
		
		public static function get_term_by_slug_path($slug_path, $taxonomy) {
			return self::get_term_by_slugs(explode('/', $slug_path), $taxonomy);
		}
		
		public static function get_terms_by_slug_paths($slug_paths, $taxonomy) {
			$return_array = array();
			foreach($slug_paths as $slug_path) {
				$return_array[] = self::get_term_by_slugs(explode('/', $slug_path), $taxonomy);
			}
			return $return_array;
		}
		
		public static function get_ids_from_terms($terms) {
			$return_array = array();
			foreach($terms as $term) {
				if($term) {
					$return_array[] = $term->term_id;
				}
			}
			return $return_array;
		}
		
		public static function filter_force_original_slug($slug, $term, $original_slug) {
			return $original_slug;
		}
		
		public static function ensure_term($path, $taxonomy) {
			$slugs = array();
			$path_slugs = explode('/', $path);
			foreach($path_slugs as $slug) {
				$slugs[] = $slug;
				self::add_term($slug, $slugs, $taxonomy);
			}
		}
		
		public static function add_term($name, $path_slugs, $taxonomy) {
			
			$current_term = self::get_term_by_slugs($path_slugs, $taxonomy);
			if($current_term) {
				return $current_term;
			}
			
			$object_slug = array_pop($path_slugs);
			$parent_term_id = 0;
			if(!empty($path_slugs)) {
				$parent_term = self::get_term_by_slugs($path_slugs, $taxonomy);
				if($parent_term) {
					$parent_term_id = $parent_term->term_id;
				}
				else {
					//METODO: error message
					return null;
				}
			}
			
			$args = array(
				'slug' => $object_slug,
				'parent' => $parent_term_id
			);
			
			add_filter('wp_unique_term_slug', array(__CLASS__, 'filter_force_original_slug'), 9999, 3);
			$new_term = wp_insert_term($name, $taxonomy, $args);
			remove_filter('wp_unique_term_slug', array(__CLASS__, 'filter_force_original_slug'), 9999, 3);
		}
		
		public static function get_all_children_of_term($parent_id, $taxonomy) {
			$args = array(
				'taxonomy' => $taxonomy,
				'parent' => $parent_id,
				'hide_empty' => false
			);
				
			return get_terms($args);
		}
		
		public static function get_single_post_id_by_term($term) {
			
			$args = array(
				'post_type' => get_post_types(array(), 'names'),
				'fields' => 'ids',
				'tax_query' => array(
					array(
						'taxonomy' => $term->taxonomy,
						'field' => 'id',
						'terms' => $term->term_id,
						'include_children' => false
					)
				)
			);
			
			$ids = get_posts($args);
			
			if(!empty($ids)) {
				return $ids[0];
			}
			
			//METODO: add warning
			return 0;
		}
		
		public static function get_full_term_slug($term, $taxonomy) {
			$return_string = $term->slug;
			if($term->parent !== 0) {
				$parent_term = get_term_by('id', $term->parent, $taxonomy);
				$return_string = self::get_full_term_slug($parent_term, $taxonomy).$return_string;
			}
			return $return_string;
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Utils\TaxonomyFunctions<br />");
		}
	}
?>