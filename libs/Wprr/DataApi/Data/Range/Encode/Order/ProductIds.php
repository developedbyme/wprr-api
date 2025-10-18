<?php
	namespace Wprr\DataApi\Data\Range\Encode\Order;
	
	class ProductIds {
		
		function __construct() {
			//echo("\Wprr\DataApi\Data\Range\Encode\Order\ProductIds::__construct<br />");
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("ProductIds::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$query = 'SELECT order_item_id as id, order_item_name as name, order_item_type as type FROM '.DB_TABLE_PREFIX.'woocommerce_order_items WHERE order_id = '.((int)$id);
			$rows = $wprr_data_api->database()->query($query);
			
			$ids = array();
			$items = array();
				
			foreach($rows as $row) {
				$current_line_item = array(
					'type' => $row['type'],
					'name' => $row['name'],
					'meta' => array()
				);
				
				$items[$row['id']] = $current_line_item;
				$ids[] = $row['id'];
			}
			
			if(!empty($ids)) {
				$query = 'SELECT order_item_id as id, meta_key, meta_value FROM '.DB_TABLE_PREFIX.'woocommerce_order_itemmeta WHERE order_item_id IN ('.implode(',', $ids).') AND meta_key = "_product_id"';
				$rows = $wprr_data_api->database()->query($query);
			
				foreach($rows as $row) {
					$id = $row['id'];
					$items[$id]['product'] = $row['meta_value'];
				}
			}
			
			$line_items = array();
			
			foreach($items as $id => $item) {
				if($item['type'] === 'line_item') {
					$line_items[] = $wprr_data_api->range()->encode_object_as((int)$item['product'], 'id');;
				}
			}
			
			$encoded_data->data['products'] = $line_items;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Data\Range\Encode\Order\ProductIds<br />");
		}
	}
?>