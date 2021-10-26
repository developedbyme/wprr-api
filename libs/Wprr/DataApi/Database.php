<?php
	namespace Wprr\DataApi;

	// \Wprr\DataApi\Database
	class Database {
		
		protected $_db = null;

		function __construct() {
			
		}
		
		public function start_session() {
			if(!$this->_db) {
				mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
				$this->_db = new \mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			}
			
			return $this;
		}
		
		public function query($query) {
			$this->start_session();
			$result = $this->_db->query($query);
			
			return $result->fetch_all(MYSQLI_ASSOC);
		}
		
		public function query_first($query) {
			$this->start_session();
			$result = $this->_db->query($query);
			
			return $result->fetch_assoc();
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