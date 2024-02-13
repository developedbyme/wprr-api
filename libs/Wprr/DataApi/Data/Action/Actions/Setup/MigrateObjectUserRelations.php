<?php
	namespace Wprr\DataApi\Data\Action\Actions\Setup;

	// \Wprr\DataApi\Data\Action\Actions\Setup\MigrateObjectUserRelations
	class MigrateObjectUserRelations {

		function __construct() {
			
		}
		
		public static function apply_action($return_value, $data) {
			//var_dump("MigrateObjectUserRelations::apply_action");
			
			global $wprr_data_api;
			
			//$wprr_data_api->database()->enable_error_reports();
			
			$return_data = array();
			
			/*
			$query =  $wprr_data_api->database()->new_select_query();
			
			$query->include_draft();
			$query->include_private();
			$query->set_post_type('dbm_object_relation');
			$query->term_query_by_path('dbm_type', 'object-relation');
			
			var_dump($query->get_query());
			*/
			
			$type_parent_term = $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term('object-user-relation');
			$term_id = $type_parent_term->get_id();
			$limit = 1000;
			
			$sql = 'SELECT '.DB_TABLE_PREFIX.'posts.ID as id FROM '.DB_TABLE_PREFIX.'posts LEFT JOIN '.DB_TABLE_PREFIX.'dbm_object_user_relations ON '.DB_TABLE_PREFIX.'posts.ID = '.DB_TABLE_PREFIX.'dbm_object_user_relations.id INNER JOIN '.DB_TABLE_PREFIX.'term_relationships ON '.DB_TABLE_PREFIX.'posts.ID = '.DB_TABLE_PREFIX.'term_relationships.object_id WHERE '.DB_TABLE_PREFIX.'dbm_object_user_relations.id IS NULL AND post_status in ("publish","draft","private") AND post_type in ("dbm_object_relation") AND '.DB_TABLE_PREFIX.'term_relationships.term_taxonomy_id = '.$term_id.' LIMIT '.$limit;
			$result = $wprr_data_api->database()->query($sql);
			
			$ids = array_map(function($row) {return (int)$row['id'];}, $result);
			
			foreach($ids as $id) {
				$post = $wprr_data_api->wordpress()->get_post($id);
				
				$from = (int)$post->get_meta('fromId');
				$to = (int)$post->get_meta('toId');
				
				$start_at = (int)$post->get_meta('startAt');
				$end_at = (int)$post->get_meta('endAt');
				
				$type = $post->get_single_term_in($type_parent_term);
				
				
				$sql = 'INSERT INTO '.DB_TABLE_PREFIX.'dbm_object_user_relations (id, postId, userId, type, startAt, endAt) SELECT '.$id.', '.$from.', '.$to.', id, '.$start_at.', '.$end_at.' FROM '.DB_TABLE_PREFIX.'dbm_object_relation_types WHERE path = "'.$type->get_slug().'";';
				$wprr_data_api->database()->insert($sql);
			}
			
			$return_data['ids'] = $ids;
			
			return $return_data;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\MigrateObjectUserRelations<br />");
		}
	}
?>