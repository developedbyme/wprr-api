<?php
	namespace Wprr\DataApi\Data\Range\Encode\Order;
	
	class Details {
		
		function __construct() {
			//echo("\Wprr\DataApi\Data\Range\Encode\Order\Details::__construct<br />");
			
		}
		
		public function prepare($ids) {
			global $wprr_data_api;
			$wprr_data_api->wordpress()->load_meta_for_posts($ids);
		}
		
		public function encode($id) {
			//var_dump("Details::encode");
			
			global $wprr_data_api;
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			$encoded_data = $wprr_data_api->range()->get_encoded_object($id);
			
			$billing = array();
			
			$billing['name'] = array(
				'firstName' => $post->get_meta('_billing_first_name'),
				'lastName' => $post->get_meta('_billing_last_name')
			);
			$billing['company'] = $post->get_meta('_billing_company');
			$billing['phoneNumber'] = $post->get_meta('_billing_phone_number');
			
			$billing['address'] = array(
				'address1' => $post->get_meta('_billing_address_1'),
				'address2' => $post->get_meta('_billing_address_2'),
				'city' => $post->get_meta('_billing_city'),
				'postCode' => $post->get_meta('_billing_postcode'),
				'country' => $post->get_meta('_billing_country'),
			);
			
			$encoded_data->data['billing'] = $billing;
			
			$shipping = array();
			
			$shipping['name'] = array(
				'firstName' => $post->get_meta('_shipping_first_name'),
				'lastName' => $post->get_meta('_shipping_last_name')
			);
			
			$shipping['address'] = array(
				'address1' => $post->get_meta('_shipping_address_1'),
				'address2' => $post->get_meta('_shipping_address_2'),
				'city' => $post->get_meta('_shipping_city'),
				'postCode' => $post->get_meta('_shipping_postcode'),
				'country' => $post->get_meta('_shipping_country'),
			);
			
			$encoded_data->data['shipping'] = $shipping;
			
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Data\Range\Encode\Order\Details<br />");
		}
	}
?>