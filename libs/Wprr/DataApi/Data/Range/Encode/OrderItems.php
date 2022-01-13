<?php
	namespace Wprr\DataApi\Data\Range\Encode;
	
	class OrderItems {
		
		function __construct() {
			//echo("\Wprr\DataApi\Data\Range\Encode\OrderItems::__construct<br />");
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("OrderItems::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$query = 'SELECT order_item_id as id, order_item_name as name, order_item_type as type FROM wp_woocommerce_order_items WHERE order_id = '.((int)$id);
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
			
			$query = 'SELECT order_item_id as id, meta_key, meta_value FROM wp_woocommerce_order_itemmeta WHERE order_item_id IN ('.implode(',', $ids).')';
			$rows = $wprr_data_api->database()->query($query);
			
			foreach($rows as $row) {
				$id = $row['id'];
				
				$key = $row['meta_key'];
				if(!isset($items[$id]['meta'][$key])) {
					$items[$id]['meta'][$key] = array();
				}
				$items[$id]['meta'][$key][] = $row['meta_value'];
			}
			
			$line_items = array();
			$coupons = array();
			
			foreach($items as $id => $item) {
				if($item['type'] === 'line_item') {
					$current_item = array();
					
					$current_item['id'] = $id;
					$current_item['quantity'] = (float)$item['meta']['_qty'][0];
					$current_item['product'] = $wprr_data_api->range()->encode_object_as((int)$item['meta']['_product_id'][0], 'product');
					$current_item['total'] = (float)$item['meta']['_line_total'][0];
					$current_item['tax'] = (float)$item['meta']['_line_tax'][0];
					
					$line_items[] = $current_item;
				}
				if($item['type'] === 'coupons') {
					$current_coupon = array();
					
					$current_item['id'] = $id;
					$current_item['code'] = $item['name'];
					$current_item['total'] = (float)$item['meta']['discount_amount'][0];
					$current_item['tax'] = (float)$item['meta']['discount_amount_tax'][0];
					
					$coupons[] = $current_item;
				}
			}
			
			$encoded_data->data['items'] = $line_items;
			$encoded_data->data['coupons'] = $coupons;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Data\Range\Encode\OrderItems<br />");
		}
	}
?>