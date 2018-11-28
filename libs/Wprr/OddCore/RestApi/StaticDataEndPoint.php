<?php
	namespace Wprr\OddCore\RestApi;
	
	use \WP_Query;
	use Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	class StaticDataEndPoint extends EndPoint {
		
		protected $_data = null;
		
		function __construct() {
			//echo("\OddCore\RestApi\StaticDataEndPoint::__construct<br />");
			
		}
		
		public function set_data($data) {
			
			$this->_data = $data;
			
			return $this;
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\StaticDataEndPoint::perform_call<br />");
			
			return $this->output_success($this->_data);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\StaticDataEndPoint<br />");
		}
	}
?>