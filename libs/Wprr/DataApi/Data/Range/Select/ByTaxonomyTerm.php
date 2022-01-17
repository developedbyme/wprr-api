<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\ByTaxonomyTerm
	class ByTaxonomyTerm {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("ByTaxonomyTerm::select");
			
			global $wprr_data_api;
			
			
			if(!isset($data['term'])) {
				throw(new \Exception('No term set'));
			}
			
			$term_groups = explode(',', $data['term']);
			
			foreach($term_groups as $term_group) {
				$terms = explode('|', $term_group);
				$ids = array();
				foreach($terms as $term_and_taxonomy) {
					$temp_array = explode(':', $term_and_taxonomy);
					$taxonomy = $temp_array[0];
					$term_name = $temp_array[1];
					
					$term = $wprr_data_api->wordpress()->get_taxonomy($taxonomy)->get_term($term_name);
					
					$ids = array_merge($ids, $term->get_ids());
				}
				$query->include_only($ids);
			}
		}
		
		public function filter($posts, $data) {
			//var_dump("ByTaxonomyTerm::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ByTaxonomyTerm<br />");
		}
	}
?>