<?php
	namespace Wprr\DataApi\WordPress\Editor;

	class WoocommerceEditor {
		
		function __construct() {
			
		}
		
		public function create_order($parent = 0) {
			$date = date('Y-m-d H:i:s');

			$type = 'shop_order';
			$title = 'Order '.$date;

			$post = wprr_get_data_api()->wordpress()->editor()->create_post($type, $title, $parent);
			$post->editor()->update_meta('_order_key', 'wc_order_' . substr( bin2hex( random_bytes(7) ), 0, 13 ));
			return $post;
		}

		public function create_subscription($parent_order = 0) {
			$date = date('Y-m-d H:i:s');

			$type = 'shop_subscription';
			$title = 'Subscription '.$date;
			$post = wprr_get_data_api()->wordpress()->editor()->create_post($type, $title, $parent_order);
			$post->editor()->update_meta('_order_key', 'wc_order_' . substr( bin2hex( random_bytes(7) ), 0, 13 ));
			return $post;
		}

		public function add_product_line_item($post, $product_post, $total, $total_tax, $subtotal, $subtotal_tax) {
			
			$fields = array(
				'order_item_name' => $product_post->get_post_title(),
        		'order_item_type' => 'line_item',
        		'order_id' => $post->get_id(),
			);
			
			$insert_statement = $this->get_insert_statement($fields);
			
			$query = 'INSERT INTO '.DB_TABLE_PREFIX.'woocommerce_order_items '.$insert_statement;
			
			$id = wprr_get_data_api()->database()->insert($query);

			//$price = 1*$product_post->get_meta('_price');

			$this->add_line_item_meta($id, '_product_id', $product_post->get_id());
			$this->add_line_item_meta($id, '_qty', 1);
			
			$this->add_line_item_meta($id, '_line_subtotal', number_format($subtotal, 2, '.', '' ));
			$this->add_line_item_meta($id, '_line_total', number_format($total, 2, '.', '' ));

			$this->add_line_item_meta($id, '_line_subtotal_tax', number_format($subtotal_tax, 2, '.', '' ));
			$this->add_line_item_meta($id, '_line_tax', number_format($total_tax, 2, '.', '' ));

			$this->add_line_item_meta($id, '_line_tax_data', serialize(array(
				'total'    => array(
					1 => number_format($total_tax, 2, '.', '' ),
				),
				'subtotal' => array(
					1 => number_format($subtotal_tax, 2, '.', '' ),
				),
			)));

			return $id;
		}

		public function add_dicount_code($post, $code, $amount, $amount_tax) {

			$fields = array(
				'order_item_name' => $code,
        		'order_item_type' => 'coupon',
        		'order_id' => $post->get_id(),
			);
			
			$insert_statement = $this->get_insert_statement($fields);
			
			$query = 'INSERT INTO '.DB_TABLE_PREFIX.'woocommerce_order_items '.$insert_statement;
			
			$id = wprr_get_data_api()->database()->insert($query);

			$this->add_line_item_meta($id, 'discount_amount', number_format($amount, 2, '.', ''));
			$this->add_line_item_meta($id, 'discount_amount_tax', number_format($amount_tax, 2, '.', ''));

			return $id;
		}

		public function add_tax_line_item($post, $amount, $percentage = 25, $label = "VAT") {
			$fields = array(
				'order_item_name' => $label,
        		'order_item_type' => 'tax',
        		'order_id' => $post->get_id(),
			);
			
			$insert_statement = $this->get_insert_statement($fields);
			
			$query = 'INSERT INTO '.DB_TABLE_PREFIX.'woocommerce_order_items '.$insert_statement;
			
			$id = wprr_get_data_api()->database()->insert($query);

			$this->add_line_item_meta($id, 'rate_id', 1);
			$this->add_line_item_meta($id, 'label', $label);
			$this->add_line_item_meta($id, 'compound', '');
			$this->add_line_item_meta($id, 'tax_amount', number_format($amount, 2, '.', ''));
			$this->add_line_item_meta($id, 'shipping_tax_amount', '0');
			$this->add_line_item_meta($id, 'rate_percent', number_format($percentage, 4, '.', ''));

			return $id;
		}

		//update_post_meta( $order_id, '_cart_discount', $discount_amount );
		//update_post_meta( $order_id, '_cart_discount_tax', '0' );

		public function add_line_item_meta($line_item_id, $key, $value) {

			$fields = array(
				'order_item_id' => $line_item_id,
    			'meta_key' => $key,
    			'meta_value' => $value,
			);
			
			$insert_statement = $this->get_insert_statement($fields);
			
			$query = 'INSERT INTO '.DB_TABLE_PREFIX.'woocommerce_order_itemmeta '.$insert_statement;
			
			$id = wprr_get_data_api()->database()->insert($query);
		}

		public function get_insert_statement($fields) {
			
			global $wprr_data_api;
			
			$db = $wprr_data_api->database();
			
			$keys= array();
			$values = array();
			
			foreach($fields as $key => $value) {
				$keys[] = $key;
				
				if($value !== null) {
					$values[] = "'".$db->escape($value)."'";
				}
				else {
					$values[] = "null";
				}
			}
			
			return '('.implode(",", $keys).') VALUES ('.implode(",", $values).')';
		}
	}
?>