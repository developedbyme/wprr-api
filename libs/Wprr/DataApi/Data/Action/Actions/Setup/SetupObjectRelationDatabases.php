<?php
	namespace Wprr\DataApi\Data\Action\Actions\Setup;

	// \Wprr\DataApi\Data\Action\Actions\Setup\SetupObjectRelationDatabases
	class SetupObjectRelationDatabases {

		function __construct() {
			
		}
		
		public static function apply_action($return_value, $data) {
			//var_dump("SetupObjectRelationDatabases::apply_action");
			
			global $wprr_data_api;
			
			//$wprr_data_api->database()->enable_error_reports();
			
			$return_data = array();
			
			$charset_collate = $charset_collate = 'DEFAULT CHARACTER SET '.DB_CHARSET;
			
			if(true) {
				$sql = 'CREATE TABLE '.DB_TABLE_PREFIX."dbm_object_relation_types (
					id int(11) NOT NULL AUTO_INCREMENT,
					path VARCHAR(255) NOT NULL,
					PRIMARY KEY (id)
				) ".$charset_collate.';';
				$return_data['dbm_object_relation_types'] = $wprr_data_api->database()->query_operation($sql);
				
				$sql = 'CREATE UNIQUE INDEX path ON '.DB_TABLE_PREFIX.'dbm_object_relation_types (path)';
				$return_data['dbm_object_relation_types/index/path'] = $wprr_data_api->database()->query_operation($sql);
			}
			
			if(true) {
				$sql = 'CREATE TABLE '.DB_TABLE_PREFIX."dbm_object_relations (
					id int(11) NOT NULL,
					fromId int(11) NOT NULL,
					toId int(11) NOT NULL,
					type int(11) NOT NULL,
					startAt int(11) NOT NULL DEFAULT '-1',
					endAt int(11) NOT NULL DEFAULT '-1',
					PRIMARY KEY (id)
				) ".$charset_collate.';';
				$return_data['dbm_object_relations'] = $wprr_data_api->database()->query_operation($sql);
				
				$sql = 'CREATE INDEX fromId ON '.DB_TABLE_PREFIX.'dbm_object_relations (fromId, type)';
				$return_data['dbm_object_relations/index/fromId'] = $wprr_data_api->database()->query_operation($sql);
				
				$sql = 'CREATE INDEX toId ON '.DB_TABLE_PREFIX.'dbm_object_relations (toId, type)';
				$return_data['dbm_object_relations/index/toId'] = $wprr_data_api->database()->query_operation($sql);
			}
			
			if(true) {
				$sql = 'CREATE TABLE '.DB_TABLE_PREFIX."dbm_object_user_relations (
					id int(11) NOT NULL,
					userId int(11) NOT NULL,
					postId int(11) NOT NULL,
					type int(11) NOT NULL,
					startAt int(11) NOT NULL DEFAULT '-1',
					endAt int(11) NOT NULL DEFAULT '-1',
					PRIMARY KEY (id)
				) ".$charset_collate.';';
				$return_data['dbm_user_object_relations'] = $wprr_data_api->database()->query_operation($sql);
				
				$sql = 'CREATE INDEX userId ON '.DB_TABLE_PREFIX.'dbm_object_user_relations (userId, type)';
				$return_data['dbm_user_object_relations/index/userId'] = $wprr_data_api->database()->query_operation($sql);
				
				$sql = 'CREATE INDEX postId ON '.DB_TABLE_PREFIX.'dbm_object_user_relations (postId, type)';
				$return_data['dbm_user_object_relations/index/postId'] = $wprr_data_api->database()->query_operation($sql);
			}
			
			return $return_data;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\SetupObjectRelationDatabases<br />");
		}
	}
?>