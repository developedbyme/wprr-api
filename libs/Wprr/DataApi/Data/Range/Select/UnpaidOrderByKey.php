<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\UnpaidOrderByKey
	class UnpaidOrderByKey {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("UnpaidOrderByKey::select");
			
			global $wprr_data_api;
			
			if(!isset($data['key'])) {
				throw(new \Exception('No key specified'));
			}
			
			$prefix = 'wc_order_';
			
			if(isset($data['prefix'])) {
				$prefix = $data['prefix'];
			}
			
			$query->set_post_type('shop_order')->set_status('wc-pending')->meta_query('_order_key', $prefix.$data['key']);
			
		}
		
		public function filter($posts, $data) {
			//var_dump("UnpaidOrderByKey::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\UnpaidOrderByKey<br />");
		}
	}
?>