<?php
	namespace Wprr\DataApi;

	// \Wprr\DataApi\Database
	class Database {
		
		protected $_db = null;
		protected $_stored_queries = array();

		function __construct() {
			
		}
		
		public function start_session() {
			if(!$this->_db) {
				mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
				$this->_db = new \mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				$this->_db->set_charset("utf8mb4"); //METODO: have this as a variable
			}
			
			return $this;
		}
		
		public function new_select_query() {
			return new \Wprr\DataApi\Data\Range\SelectQuery();
		}
		
		public function query($query) {
			global $wprr_data_api;
			
			if(!isset($this->_stored_queries[$query])) {
				
				
				$this->start_session();
				
				$wprr_data_api->performance()->count('Database::query query');
				//$wprr_data_api->performance()->count($query);
				
				$wprr_data_api->performance()->start_meassure('Database::query query');
				try {
					$result = $this->_db->query($query);
				}
				catch(\Exception $exception) {
					throw(new \Exception('SQL error: '.$exception->getMessage().' from query '.$query));
				}
				$wprr_data_api->performance()->stop_meassure('Database::query query');
				
				$wprr_data_api->performance()->start_meassure('Database::query fetch');
				$this->_stored_queries[$query] = $result->fetch_all(MYSQLI_ASSOC);
				$wprr_data_api->performance()->stop_meassure('Database::query fetch');
			}
			else {
				$wprr_data_api->performance()->count('Database::query stored');
			}
			
			return $this->_stored_queries[$query];
		}
		
		public function query_first($query) {
			return $this->query($query)[0];
		}
		
		public function query_without_storage($query) {
			global $wprr_data_api;
			
			$this->start_session();
			$wprr_data_api->performance()->count('Database::query_without_storage query');
			
			$wprr_data_api->performance()->start_meassure('Database::query_without_storage query');
			try {
				$result = $this->_db->query($query);
			}
			catch(\Exception $exception) {
				throw(new \Exception('SQL error: '.$exception->getMessage().' from query '.$query));
			}
			$wprr_data_api->performance()->stop_meassure('Database::query_without_storage query');
			
			$wprr_data_api->performance()->start_meassure('Database::query_without_storage fetch');
			$rows = $result->fetch_all(MYSQLI_ASSOC);
			$wprr_data_api->performance()->stop_meassure('Database::query_without_storage fetch');
			
			return $rows;
		}
		
		public function end_session() {
			if($this->_db) {
				$this->_db->close();
				$this->_db = null;
			}
			
			return $this;
		}
		
		public function escape($value) {
			$this->start_session();
			return $this->_db->real_escape_string($value);
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\Database<br />");
		}
	}
?>