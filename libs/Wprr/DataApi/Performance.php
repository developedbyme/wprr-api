<?php
	namespace Wprr\DataApi;

	// \Wprr\DataApi\Performance
	class Performance {
		
		protected $_call_performance = array();
		protected $_counts = array();
		
		function __construct() {
			
		}
		
		public function count($type) {
			if(!isset($this->_counts[$type])) {
				$this->_counts[$type] = 0;
			}
			$this->_counts[$type]++;
		}
		
		public function &get_call_performance($type) {
			if(!isset($this->_call_performance[$type])) {
				$this->_call_performance[$type] = array('calls' => array(), 'currentStartTime' => 0);
			}
			
			return $this->_call_performance[$type];
		}
		
		public function start_meassure($type) {
			$stats = &$this->get_call_performance($type);
			$stats['currentStartTime'] = microtime(true);
		}
		
		public function stop_meassure($type) {
			
			$end_time = microtime(true);
			
			$stats = &$this->get_call_performance($type);
			$call_time = $end_time-$stats['currentStartTime'];
			$stats['calls'][] = $call_time;
		}
		
		public function get_stats() {
			
			$return_object = array();
			foreach($this->_call_performance as $type => $data) {
				
				$number_of_calls = count($data['calls']);
				$total_time = array_sum($data['calls']);
				if($number_of_calls) {
					$average_time = $total_time/$number_of_calls;
				}
				else {
					$number_of_calls = 0;
				}
				
				$return_object[$type] = array(
					'numberOfCalls' => $number_of_calls,
					'total' => $total_time,
					'average' => $average_time
				);
			}
			
			return array(
				'speed' => $return_object,
				'count' => $this->_counts
			);
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Performance<br />");
		}
	}
?>