<?php
	namespace Wprr\DataApi\Data\Action\Actions\Setup;

	// \Wprr\DataApi\Data\Action\Actions\Setup\CopyObjectRelationTypesFromTerms
	class CopyObjectRelationTypesFromTerms {

		function __construct() {
			
		}
		
		public static function apply_action($return_value, $data) {
			//var_dump("CopyObjectRelationTypesFromTerms::apply_action");
			
			global $wprr_data_api;
			
			//$wprr_data_api->database()->enable_error_reports();
			
			$return_data = array();
			
			$terms = $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term('object-relation')->get_children();
			
			$slugs = array_map(function($term) {return $term->get_slug();}, $terms);
			
			$terms = $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term('object-user-relation')->get_children();
			
			$slugs = array_unique(array_merge($slugs, array_map(function($term) {return $term->get_slug();}, $terms)));
			
			$values_array = array_map(function($slug) {
				global $wprr_data_api;
				return "('".$wprr_data_api->database()->escape($slug)."')";
			}, $slugs);
			
			$sql = 'INSERT IGNORE INTO '.DB_TABLE_PREFIX."dbm_object_relation_types (path) VALUES ".implode(", ", $values_array).";";
			$return_data['insert'] = $wprr_data_api->database()->query_operation($sql);
			
			return $return_data;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\CopyObjectRelationTypesFromTerms<br />");
		}
	}
?>