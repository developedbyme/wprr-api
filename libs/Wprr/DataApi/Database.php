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
				mysqli_set_charset($this->_db, "utf8");
			}
			
			return $this;
		}
		
		public function new_select_query() {
			return new \Wprr\DataApi\Data\Range\SelectQuery();
		}
		
		public function query($query) {
			
			if(!isset($this->_stored_queries[$query])) {
				$this->start_session();
				$result = $this->_db->query($query);
				
				$this->_stored_queries[$query] = $result->fetch_all(MYSQLI_ASSOC);
			}
			
			return $this->_stored_queries[$query];
		}
		
		public function query_first($query) {
			return $this->query($query)[0];
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