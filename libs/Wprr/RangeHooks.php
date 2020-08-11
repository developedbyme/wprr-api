<?php
	namespace Wprr;
	
	use \WP_Query;
	
	// \Wprr\RangeHooks
	class RangeHooks {
		
		function __construct() {
			//echo("\Wprr\RangeHooks::__construct<br />");
			
			
		}
		
		protected function add_tax_query(&$query_args, $tax_query, $relation = 'AND') {
			if(isset($query_args['tax_query'])) {
				$combined_query = array(
					'relation' => $relation,
					$tax_query,
					$query_args['tax_query']
				);
				
				$query_args['tax_query'] = $combined_query;
			}
			else {
				$query_args['tax_query'] = array($tax_query);
			}
			
			return $query_args;
		}
		
		protected function get_term_ids($data) {
			$return_array = array();
			
			$terms = explode(',', $data['terms']);
			$termsField = isset($data['termsField']) ? $data['termsField'] : 'slugPath';
			
			foreach($terms as $term) {
				if($termsField === 'slugPath') {
					$current_term = \Wprr\OddCore\Utils\TaxonomyFunctions::get_term_by_slug_path($term, $data['taxonomy']);
				}
				else {
					$current_term = get_term_by($termsField, $term, $data['taxonomy']);
				}
				
				if($current_term) {
					$return_array[] = $current_term->term_id;
				}
			}
			
			return $return_array;
		}
		
		protected function require_paramters($data, $parameters) {
			$parameters_array = explode(',', $parameters);
			$is_ok = true;
			$missing_parameters = array();
			
			foreach($parameters_array as $parameter) {
				if(!isset($data[$parameter])) {
					$is_ok = false;
					$missing_parameters[] = $parameter;
				}
			}
			
			if(!$is_ok) {
				throw(new \Exception("Missing parameters: ".implode(', ', $missing_parameters)));
			}
			
			return $this;
		}
		
		protected function require_logged_in() {
			if(!is_user_logged_in()) {
				throw(new \Exception("Request requires user to be logged in"));
			}
			
			return $this;
		}
		
		protected function register_hook_for_type($type, $hook_name) {
			
		}
		
		public function register() {
			//echo("\Wprr\RangeHooks::register<br />");
			
			add_filter(WPRR_DOMAIN.'/range_query/standard', array($this, 'filter_query_standard'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/default', array($this, 'filter_query_standard'), 10, 2);
			//add_filter(WPRR_DOMAIN.'/range_selection_has_permission/drafts', array('\Wprr\PermissionFilters', 'waterfall_is_admin'), 10, 1);
			add_filter(WPRR_DOMAIN.'/range_query/drafts', array($this, 'filter_query_drafts'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/onlyDrafts', array($this, 'filter_query_onlyDrafts'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/privates', array($this, 'filter_query_privates'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/trashed', array($this, 'filter_query_trashed'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/attachmentStatus', array($this, 'filter_query_attachment_status'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/idSelection', array($this, 'filter_query_id_selection'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/idSelectionWithTranslation', array($this, 'filter_query_idSelectionWithTranslation'), 10, 2);
			
			add_filter(WPRR_DOMAIN.'/range_query/myOrders', array($this, 'filter_query_my_orders'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_filter/myOrders', array($this, 'filter_filter_my_orders'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/myOrder', array($this, 'filter_query_my_order'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/inTaxonomy', array($this, 'filter_query_in_taxonomy'), 10, 2);
			
			add_filter(WPRR_DOMAIN.'/range_query/allOrders', array($this, 'filter_query_allOrders'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/byOrderStatus', array($this, 'filter_query_byOrderStatus'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/byCompletedDate', array($this, 'filter_query_byCompletedDate'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/allSubscriptions', array($this, 'filter_query_allSubscriptions'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/activeSubscriptions', array($this, 'filter_query_activeSubscriptions'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/byProduct', array($this, 'filter_query_byProduct'), 10, 2);
			
			add_filter(WPRR_DOMAIN.'/range_encoding/id', array($this, 'filter_encode_id'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/standard', array($this, 'filter_encode_standard'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/default', array($this, 'filter_encode_standard'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/privateTitle', array($this, 'filter_encode_privateTitle'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/status', array($this, 'filter_encode_status'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/translations', array($this, 'filter_encode_translations'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/attachment', array($this, 'filter_encode_attachment'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/preview', array($this, 'filter_encode_preview'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/rawTextsForTranslation', array($this, 'filter_encode_rawTextsForTranslation'), 10, 3);
			
			add_filter(WPRR_DOMAIN.'/range_encoding/editFields', array($this, 'filter_encode_standard'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/editFields', array($this, 'filter_encode_edit_fields'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/editFields', array($this, 'filter_encode_status'), 10, 3);
			
			add_filter(WPRR_DOMAIN.'/range_encoding/fullPost', array($this, 'filter_encode_full_post'), 10, 3);
			
			add_filter(WPRR_DOMAIN.'/range_encoding/order', array($this, 'filter_encode_order'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/orderCompletedDate', array($this, 'filter_encode_orderCompletedDate'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/subscriptionNextDate', array($this, 'filter_encode_subscriptionNextDate'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/subscriptionForOrder', array($this, 'filter_encode_subscriptionForOrder'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/subscription', array($this, 'filter_encode_order'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/subscription', array($this, 'filter_encode_subscription'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/usersOtherSubscriptions', array($this, 'filter_encode_usersOtherSubscriptions'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/customerName', array($this, 'filter_encode_customerName'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/subscriptionEnd', array($this, 'filter_encode_subscriptionEnd'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/product', array($this, 'filter_encode_product'), 10, 3);
			
			add_filter(WPRR_DOMAIN.'/user_encoding/customer', array($this, 'filter_encode_user_customer'), 10, 3);
		}
		
		public function filter_query_standard($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_standard<br />");
			
			//MENOTE: do nothing
			
			return $query_args;
		}
		
		public function filter_query_id_selection($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_id_selection<br />");
			
			$this->require_paramters($data, 'ids');
			
			$query_args['post__in'] = explode(',', $data['ids']);
			
			
			
			return $query_args;
		}
		
		public function filter_query_idSelectionWithTranslation($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_idSelectionWithTranslation<br />");
			
			$this->require_paramters($data, 'ids');
			
			global $sitepress;
			
			$translated_ids = array();
			$ids = explode(',', $data['ids']);
			foreach($ids as $id) {
				if($sitepress) {
					$id = apply_filters('wpml_object_id', $id, $query_args['post_type'], true, $sitepress->get_current_language());
				}
				$translated_ids[] = $id;
			}
			
			$query_args['post__in'] = $translated_ids;
			
			return $query_args;
		}
		
		
		public function filter_query_in_taxonomy($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_in_taxonomy<br />");
			
			$this->require_paramters($data, 'taxonomy');
			//METODO: check taxonomy ids
			
			$term_ids = $this->get_term_ids($data);
			
			$current_tax_query = array(
				'taxonomy' => $data['taxonomy'],
				'field' => 'id',
				'terms' => $term_ids,
				'include_children' => (($data['includeTermChildren'] == '1') ? true : false)
			);
			$this->add_tax_query($query_args, $current_tax_query);
			
			return $query_args;
		}
		
		public function filter_query_drafts($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_drafts<br />");
			
			if(!current_user_can('edit_others_posts')) {
				$query_args['post__in'] = array(0);
				return $query_args;
			}
			
			if(!isset($query_args['post_status'])) {
				$query_args['post_status'] = array('publish');
			}
			
			$query_args['post_status'][] = 'draft';
			$query_args['post_status'][] = 'pending';
			
			return $query_args;
		}
		
		public function filter_query_onlyDrafts($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_onlyDrafts<br />");
			
			if(!current_user_can('edit_others_posts')) {
				$query_args['post__in'] = array(0);
				return $query_args;
			}
			
			$query_args['post_status'] = array();
			$query_args['post_status'][] = 'draft';
			$query_args['post_status'][] = 'pending';
			
			return $query_args;
		}
		
		public function filter_query_trashed($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_trashed<br />");
			
			/*
			if(!current_user_can('read_private_posts')) {
				$query_args['post__in'] = array(0);
				return $query_args;
			}
			*/
			
			if(!isset($query_args['post_status'])) {
				$query_args['post_status'] = array('publish');
			}
			
			$query_args['post_status'][] = 'trash';
			
			return $query_args;
		}
		
		public function filter_query_privates($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_privates<br />");
			
			/*
			if(!current_user_can('read_private_posts')) {
				
				throw(new \Exception('User doesn\'t have permission to read private posts'));
				
				$query_args['post__in'] = array(0);
				return $query_args;
			}
			*/
			
			if(!isset($query_args['post_status'])) {
				$query_args['post_status'] = array('publish');
			}
			
			$query_args['post_status'][] = 'private';
			
			return $query_args;
		}
		
		public function filter_query_my_orders($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_my_orders<br />");
			
			if(!isset($query_args['post_status'])) {
				$query_args['post_status'] = array('publish');
			}
			
			$order_statuses = wc_get_order_statuses();
			foreach($order_statuses as $key => $label) {
				$query_args['post_status'][] = $key;
			}
			
			return $query_args;
		}
		
		public function filter_filter_my_orders($ids, $data) {
			//echo("\Wprr\RangeHooks::filter_filter_my_orders<br />");
			
			if(current_user_can('edit_others_posts') && false) {
				return $ids;
			}
			
			$current_user_id = get_current_user_id();
			
			if(!$current_user_id) {
				return array();
			}
			
			$qualified_ids = array();
			foreach($ids as $id) {
				$order_user_id = (int)get_post_meta($id, '_customer_user', true);
				if($order_user_id === $current_user_id) {
					$qualified_ids[] = $id;
				}
			}
			
			return $qualified_ids;
		}
		
		public function filter_query_my_order($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_my_order<br />");
			
			$this->require_paramters($data, 'id');
			$this->require_logged_in();
			
			if(!isset($query_args['post_status'])) {
				$query_args['post_status'] = array('publish');
			}
			
			$order_statuses = wc_get_order_statuses();
			foreach($order_statuses as $key => $label) {
				$query_args['post_status'][] = $key;
			}
			
			$id = $data['id'];
			
			$current_user_id = get_current_user_id();
			$order_user_id = (int)get_post_meta($id, '_customer_user', true);
			
			$is_owner_or_admin = (current_user_can('edit_others_posts') || ($order_user_id === $current_user_id));
			
			$can_view_orders = apply_filters(WPRR_DOMAIN.'/current_user_can_get_private_order_data', $is_owner_or_admin);
			
			if(!$can_view_orders) {
				throw(new \Exception('Not permitted (current user: '.$current_user_id.', order user: '.$order_user_id.')'));
			}
			
			$query_args['post__in'] = array($id);
			
			return $query_args;
		}
		
		public function filter_query_attachment_status($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_attachment_status<br />");
			
			if(!isset($query_args['post_status'])) {
				$query_args['post_status'] = array('publish');
			}
			
			$query_args['post_status'][] = 'inherit';
			
			return $query_args;
		}
		
		public function filter_encode_id($encoded_data, $post_id, $data) {
			//echo("\Wprr\RangeHooks::filter_encode_id<br />");
			
			//MENOTE: do nothing
			
			return $encoded_data;
		}
		
		public function filter_encode_standard($encoded_data, $post_id, $data) {
			//echo("\Wprr\RangeHooks::filter_encode_standard<br />");
			
			$encoded_data["permalink"] = get_permalink($post_id);
			$encoded_data["title"] = get_the_title($post_id);
			
			return $encoded_data;
		}
		
		public function filter_encode_privateTitle($encoded_data, $post_id, $data) {
			//echo("\Wprr\RangeHooks::filter_encode_privateTitle<br />");
			
			$encoded_data["title"] = get_post($post_id)->post_title;
			
			return $encoded_data;
		}
		
		public function filter_encode_preview($encoded_data, $post_id, $data) {
			//echo("\Wprr\RangeHooks::filter_encode_preview<br />");
			
			$encoded_data["permalink"] = get_permalink($post_id);
			$encoded_data["title"] = get_the_title($post_id);
			$encoded_data["excerpt"] = get_the_excerpt($post_id);
			$encoded_data["image"] = wprr_encode_post_image($post_id);
			
			return $encoded_data;
		}
		
		public function filter_encode_edit_fields($encoded_data, $post_id, $data) {
			
			$encoded_data["_thumbnail_id"] = get_post_meta($post_id, '_thumbnail_id', true);
			
			$post = get_post($post_id); 
			
			$encoded_data["slug"] = $post->post_name;
			$encoded_data["parent"] = $post->parent;
			
			$post_type = get_post_type($post_id);
			
			$encoded_data = apply_filters(WPRR_DOMAIN.'/edit_fields/type/'.$post_type, $encoded_data, $post_id, $data);
			
			return $encoded_data;
		}
		
		public function filter_encode_status($encoded_data, $post_id, $data) {
			//echo("\Wprr\RangeHooks::filter_encode_status<br />");
			
			$encoded_data['status'] = get_post_status($post_id);
			
			return $encoded_data;
		}
		
		public function filter_encode_attachment($encoded_data, $post_id, $data) {
			//echo("\Wprr\RangeHooks::filter_encode_attachment<br />");
			$encoder = new \Wprr\WprrEncoder();
			
			return $encoder->encode_image(get_post($post_id));
		}
		
		public function filter_encode_translations($encoded_data, $post_id, $data) {
			//echo("\Wprr\RangeHooks::filter_encode_translations<br />");
			
			global $sitepress;
			
			if($sitepress) {
				
				$post = get_post($post_id);
				
				$t_post_id = $sitepress->get_element_trid($post_id, 'post_dp_template' );
				$translations = $sitepress->get_element_translations($t_post_id, 'post_'.($post->post_type), false, true);
				
				$return_langauges = array();
				
				$wprr_encoder = new \Wprr\WprrEncoder();
				
				foreach($translations as $language_code => $translation) {
					$current_translation = array(
						'language' => $language_code,
						'post' => $wprr_encoder->encode_post_link_in_language($translation->element_id, $language_code)
					);
					
					$return_langauges[] = $current_translation;
				}
				
				$encoded_data["languages"] = $return_langauges;
			}
			
			return $encoded_data;
		}
		
		public function filter_encode_rawTextsForTranslation($encoded_data, $post_id, $data) {
			//echo("\Wprr\RangeHooks::filter_encode_rawTextsForTranslation<br />");
			
			global $sitepress;
			
			if($sitepress) {
				
				$post = get_post($post_id);
				
				$t_post_id = $sitepress->get_element_trid($post_id, 'post_'.($post->post_type) );
				$translations = $sitepress->get_element_translations($t_post_id, 'post_'.($post->post_type), false, true);
				
				$return_langauges = array();
				
				$wprr_encoder = new \Wprr\WprrEncoder();
				
				foreach($translations as $language_code => $translation) {
					
					$translated_post = get_post($translation->element_id);
					
					$current_translation = array(
						'language' => $language_code,
						'title' => $translated_post->post_title,
						'content' => $translated_post->post_content
					);
					
					$return_langauges[] = $current_translation;
				}
				
				$encoded_data["rawTranslations"] = $return_langauges;
			}
			
			return $encoded_data;
		}
		
		public function filter_encode_full_post($encoded_data, $post_id, $data) {
			//echo("\Wprr\RangeHooks::filter_encode_full_post<br />");
			
			$encoded_data = mrouter_encode_post(get_post($post_id));
			
			return $encoded_data;
		}
		
		public function filter_encode_order($return_object, $post_id) {
			//echo("\Wprr\RangeHooks::filter_encode_order<br />");
			
			$order = new \WC_Order($post_id);
			
			$return_object['status'] = $order->get_status();
			$return_object['currency'] = $order->get_currency();
			$return_object['date'] = $order->get_date_created()->date('Y-m-d H:i:s');
			$return_object['user'] = wprr_encode_user($order->get_user());
			$return_object['coupons'] = $order->get_used_coupons();
			$return_object['totals'] = array(
				'total' => $order->get_total(),
				'discount_total' => $order->get_discount_total()
			);
			$return_object['taxTotals'] = $order->get_tax_totals();
			
			$billing_attributes = array('first_name', 'last_name', 'company', 'address_1', 'address_2', 'postcode', 'city', 'country', 'email', 'phone');
			$billing_details = array();
			foreach($billing_attributes as $billing_attribute) {
				$get_function_name = 'get_billing_'.$billing_attribute;
				$billing_details[$billing_attribute] = $order->$get_function_name();
			}
			$return_object['contactDetails'] = array('billing' => $billing_details);
			
			$return_object['paymentMethod'] = $order->get_payment_method();
			
			$line_items = $order->get_items();
			
			$encoded_items = array();
			foreach($line_items as $line_item) {
				$encoded_item = array(
					'quantity' => $line_item->get_quantity(),
					'product' => mrouter_encode_post_link($line_item->get_product_id()),
					'total' => $line_item->get_total()
				);
				
				$encoded_items[] = $encoded_item;
			}
			
			$return_object['items'] = $encoded_items;
			
			return $return_object;
		}
		
		public function filter_encode_orderCompletedDate($return_object, $post_id) {
			//echo("\Wprr\RangeHooks::filter_encode_orderCompletedDate<br />");
			
			$completed_date = get_post_meta($post_id, '_completed_date', true);
			
			if($completed_date) {
				$return_object['completedDate'] = date('Y-m-d', strtotime($completed_date));
			}
			
			
			return $return_object;
		}
		
		public function filter_encode_subscriptionNextDate($return_object, $post_id) {
			//echo("\Wprr\RangeHooks::filter_encode_subscriptionNextDate<br />");
			
			$subscription = new \WC_Subscription($post_id);
			
			$return_object['nextDate'] = date('Y-m-d', strtotime($subscription->get_date('next_payment')));
			
			return $return_object;
		}
		
		
		
		public function filter_encode_subscriptionForOrder($return_object, $post_id) {
			$time_zone = get_option('timezone_string');
			$subscriptions = wcs_get_subscriptions_for_order($post_id, array( 'order_type' => ['parent', 'switch', 'renewal'] ));
			
			if($subscriptions && !empty($subscriptions)) {
				foreach($subscriptions as $subscription) {
					
					$subscription_id = $subscription->get_id();
					
					$return_object['subscription'] = array(
						'id' => $subscription_id,
						'lastPayment' => $subscription->get_date('last_payment', $time_zone),
						'renewalCycle' => array(
							'interval' => (float)$subscription->get_billing_interval(),
							'period' => $subscription->get_billing_period()
						)
					);
					break;
				}
			}
			
			return $return_object;
		}
		
		protected function verify_orders_permission() {
			$this->require_logged_in();
			$current_user_id = get_current_user_id();
			
			$can_view_orders = apply_filters(WPRR_DOMAIN.'/current_user_can_get_private_order_data', current_user_can('edit_others_posts'));
			
			if(!$can_view_orders) {
				throw(new \Exception('Not permitted'));
			}
			
			return true;
		}
		
		public function filter_query_allOrders($query_args, $data) {
			
			$this->verify_orders_permission();
			
			$query_args['post_status'] = array_keys( wc_get_order_statuses() );
			
			return $query_args;
		}
		
		public function filter_query_byProduct($query_args, $data) {
			if(!isset($query_args['meta_query'])) {
				$query_args['meta_query'] = array();
			}
			
			$query_args['meta_query'][] = array(
				'key' => 'wprr_product_id',
				'value' => $data['productId'],
				'compare' => '=',
			);
			
			return $query_args;
		}
		
		public function filter_query_byOrderStatus($query_args, $data) {
			
			$this->verify_orders_permission();
			
			$status = $data['status'];
			$query_args['post_status'] = explode(',', $status);
			
			return $query_args;
		}
		
		public function filter_query_byCompletedDate($query_args, $data) {
			
			if(!isset($query_args['meta_query'])) {
				$query_args['meta_query'] = array();
			}
			
			$query_args['meta_query'][] = array(
				'key' => '_completed_date',
				'value' => array($data['startDate'], $data['endDate']),
				'compare' => 'BETWEEN',
				'type' => 'DATE',
			);
			
			return $query_args;
		}
		
		public function filter_query_allSubscriptions($query_args, $data) {
			//echo("\Wprr\RangeHooks::query_allSubscriptions<br />");
			
			$this->verify_orders_permission();
			
			$query_args['post_status'] = array( 'wc-pending', 'wc-active', 'wc-on-hold', 'wc-pending-cancel', 'wc-cancelled', 'wc-expired' );
			
			return $query_args;
		}
		
		public function filter_query_activeSubscriptions($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_activeSubscriptions<br />");
			
			$this->verify_orders_permission();
			
			$query_args['post_status'] = array('wc-active');
			
			return $query_args;
		}
		
		public function filter_encode_subscription($return_object, $post_id) {
			//echo("\Wprr\RangeHooks::filter_encode_subscription<br />");
			
			$subscription = new \WC_Subscription($post_id);
			
			$related_order_ids = $subscription->get_related_orders();
			
			$encoded_orders = array();
			foreach($related_order_ids as $related_order_id) {
				$order = new \WC_Order($related_order_id);
				$encoded_order = array();
				$encoded_order['id'] = $related_order_id;
				$encoded_order['status'] = $order->get_status();
				$encoded_order['total'] = $order->get_total();
				$encoded_order['currency'] = $order->get_currency();
				$encoded_order['date'] = $order->get_date_created()->date('Y-m-d H:i:s');
				
				$encoded_orders[] = $encoded_order;
			}
			$return_object['relatedOrders'] = $encoded_orders;
			
			$date_types = array('start', 'trial_end', 'next_payment', 'last_payment', 'end');
			$encoded_dates = array();
			foreach($date_types as $date_type) {
				$encoded_dates[$date_type] = $subscription->get_date($date_type);
				if($encoded_dates[$date_type] === 0) {
					$encoded_dates[$date_type] = null;
				}
			}
			$return_object['dates'] = $encoded_dates;
			
			return $return_object;
		}
		
		public function filter_encode_customerName($return_object, $post_id) {
			//echo("\Wprr\RangeHooks::filter_encode_customerName<br />");
			
			$return_object['userId'] = (int)get_post_meta($post_id, '_customer_user', true);
			$return_object['firstName'] = get_post_meta($post_id, '_billing_first_name', true);
			$return_object['lastName'] = get_post_meta($post_id, '_billing_last_name', true);
			
			return $return_object;
		}
		
		public function filter_encode_subscriptionEnd($return_object, $post_id) {
			//echo("\Wprr\RangeHooks::filter_encode_subscriptionEnd<br />");
			
			$return_object['end'] = get_post_meta($post_id, '_schedule_end', true);
			
			return $return_object;
		}
		
		public function filter_encode_product($return_object, $post_id) {
			//echo("\Wprr\RangeHooks::filter_encode_product<br />");
			
			$product = wc_get_product($post_id);
			$return_object['price'] = $product->get_price('raw');
			$return_object['regularPrice'] = $product->get_regular_price('raw');
			$return_object['currency'] = get_woocommerce_currency();
			$return_object['description'] = $product->get_description();
			$return_object['shortDescription'] = $product->get_short_description();
			
			global $woocommerce_wpml;
			if(isset($woocommerce_wpml) && $woocommerce_wpml->multi_currency) {
				
				$currencies = $woocommerce_wpml->multi_currency->get_currencies('include_default = true');
				
				$return_object['currency'] = $woocommerce_wpml->multi_currency->get_client_currency(); 
				
				$currency_prices = array();
				foreach($currencies as $currency_id => $currency) {
					$currency_prices[$currency_id] = (float)$woocommerce_wpml->multi_currency->prices->get_product_price_in_currency($post_id, $currency_id);
				}
				$return_object['currencyPrices'] = $currency_prices;
			}
			
			$return_object['isPurchasable'] = $product->is_purchasable();
			$return_object['isOnSale'] = $product->is_on_sale();
			$return_object['isFeatured'] = $product->is_featured();
			$return_object['isInStock'] = $product->is_in_stock();
			
			$return_object['rating'] = array(
				'count' => $product->get_rating_count(),
				'totals' => $product->get_rating_counts(),
				'average' => $product->get_average_rating(),
				'reviews' => $product->get_review_count()
			);
			
			$all_languages_average = get_post_meta($post_id, '_wcml_average_rating', true);
			if($all_languages_average) {
				$return_object['rating']['average'] = $all_languages_average;
			}
			
			$all_languages_reviews = (int)get_post_meta($post_id, '_wcml_review_count', true);
			if($all_languages_reviews) {
				$return_object['rating']['count'] = $all_languages_reviews;
				$return_object['rating']['reviews'] = $all_languages_reviews;
			}
			
			return $return_object;
		}
		
		public function filter_encode_usersOtherSubscriptions($return_object, $post_id) {
			//echo("\Wprr\RangeHooks::filter_encode_usersOtherSubscriptions<br />");
			
			$user_id = get_post_meta($post_id, '_customer_user', true);
			
			$encoded_subscriptions = array();
			
			$subscription_ids = \WCS_Customer_Store::instance()->get_users_subscription_ids($user_id);
			foreach($subscription_ids as $subscription_id) {
				if($subscription_id !== $post_id) {
					$encoded_subscriptions[] = array('id' => $subscription_id, 'status' => get_post_status($subscription_id));
				}
			}
			
			$return_object['otherSubscriptions'] = $encoded_subscriptions;
			
			return $return_object;
		}
		
		public function filter_encode_user_customer($return_object, $user_id, $data) {
			
			$customer = new \WC_Customer($user_id);
			
			$current_data = $customer->get_data();
			
			$encoder = wprr_get_encoder();
			
			$return_object = $encoder->encode_user_with_private_data(get_user_by('id', $user_id));
			$return_object['isPayingCustomer'] = $current_data['is_paying_customer'];
			$return_object['contactDetails'] = array('billing' => $current_data['billing'], 'shipping' => $current_data['shipping']);
			
			return $return_object;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\RangeHooks<br />");
		}
	}
?>