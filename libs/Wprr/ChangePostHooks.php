<?php
	namespace Wprr;
	
	use \WP_Query;
	
	// \Wprr\ChangePostHooks
	class ChangePostHooks {
		
		function __construct() {
			//echo("\Wprr\ChangePostHooks::__construct<br />");
			
			
		}
		
		protected function register_hook_for_type($type, $hook_name) {
			add_action(WPRR_DOMAIN.'/admin/change_post/'.$type, array($this, $hook_name), 10, 3);
		}
		
		public function register() {
			//echo("\Wprr\ChangePostHooks::register<br />");
			
			$this->register_hook_for_type('parent', 'hook_set_parent');
			$this->register_hook_for_type('resave', 'hook_resave');
			$this->register_hook_for_type('status', 'hook_set_status');
			$this->register_hook_for_type('content', 'hook_set_content');
			$this->register_hook_for_type('slug', 'hook_set_slug');
			$this->register_hook_for_type('setSlugPath', 'hook_set_setSlugPath');
			$this->register_hook_for_type('title', 'hook_set_title');
			$this->register_hook_for_type('terms', 'hook_set_terms');
			$this->register_hook_for_type('addTerms', 'hook_add_terms');
			$this->register_hook_for_type('removeTerms', 'hook_remove_terms');
			$this->register_hook_for_type('meta', 'hook_set_meta');
			$this->register_hook_for_type('removeMeta', 'hook_removeMeta');
			$this->register_hook_for_type('trash', 'hook_trash');
			$this->register_hook_for_type('acf', 'hook_set_acf');
			$this->register_hook_for_type('wpml/createDuplicates', 'hook_wpml_create_duplicates');
			$this->register_hook_for_type('wpml/createCopies', 'hook_wpml_create_copies');
			$this->register_hook_for_type('wpml/createLanguageCopy', 'hook_wpml_createLanguageCopy');
			
			$this->register_hook_for_type('woocommerce/subscriptions/changeNextPayment', 'hook_woocommerce_subscriptions_changeNextPayment');
			$this->register_hook_for_type('woocommerce/order/paymentMethod', 'hook_woocommerce_order_paymentMethod');
			$this->register_hook_for_type('woocommerce/order/addProduct', 'hook_woocommerce_order_addProduct');
			$this->register_hook_for_type('woocommerce/order/calculateTotals', 'hook_woocommerce_order_calculateTotals');
		}
		
		protected function update_post_data($post_id, $field, $value) {
			return wp_update_post(array(
				'ID' => $post_id,
				$field => $value
			));
		}
		
		public function hook_set_parent($data, $post_id, $logger) {
			//echo("\Wprr\ChangePostHooks::hook_set_parent<br />");
			
			$this->update_post_data($post_id, 'post_parent', $data['value']);
		}
		
		public function hook_resave($data, $post_id, $logger) {
			//echo("\Wprr\ChangePostHooks::hook_resave<br />");
			
			wp_update_post(array(
				'ID' => $post_id
			));
		}
		
		public function hook_trash($data, $post_id, $logger) {
			//echo("\Wprr\ChangePostHooks::hook_trash<br />");
			
			wp_trash_post($post_id);
		}
		
		public function hook_set_status($data, $post_id, $logger) {
			//echo("\Wprr\ChangePostHooks::hook_set_status<br />");
			
			$this->update_post_data($post_id, 'post_status', $data['value']);
		}
		
		public function hook_set_content($data, $post_id, $logger) {
			//echo("\Wprr\ChangePostHooks::hook_set_content<br />");
			
			$this->update_post_data($post_id, 'post_content', $data['value']);
		}
		
		public function hook_set_slug($data, $post_id, $logger) {
			//echo("\Wprr\ChangePostHooks::hook_set_content<br />");
			
			$this->update_post_data($post_id, 'post_name', $data['value']);
		}
		
		protected function ensure_path_exists($path_array) {
			//echo("\Wprr\ChangePostHooks::ensure_path_exists<br />");
			
			$parent = 0;
			
			if(!empty($path_array)) {
				foreach($path_array as $path_part) {
					$exisiting_parent_id = dbm_new_query('page')->set_field('post_status', array('publish', 'draft', 'future', 'private'))->set_field('post_parent', $parent)->set_field('name', $path_part)->get_post_id();
					if(!$exisiting_parent_id) {
						$exisiting_parent_id = wp_insert_post(array(
							'post_title' => $path_part,
							'post_name' => $path_part,
							'post_parent' => $parent,
							'post_status' => 'publish',
							'post_type' => 'page'
						));
					}
					$parent = $exisiting_parent_id;
				}
			}
			
			return $parent;
		}
		
		public function hook_set_setSlugPath($data, $post_id, $logger) {
			//echo("\Wprr\ChangePostHooks::hook_set_setSlugPath<br />");
			
			$path = explode('/', $data['value']);
			$post_name = array_pop($path);
			
			
			$parent = $this->ensure_path_exists($path);
			
			wp_update_post(array(
				'ID' => $post_id,
				'post_name' => $post_name,
				'post_parent' => $parent
			));
		}
		
		public function hook_set_title($data, $post_id, $logger) {
			//echo("\Wprr\ChangePostHooks::hook_set_title<br />");
			
			$this->update_post_data($post_id, 'post_title', $data['value']);
		}
		
		protected function get_terms($data) {
			$terms = $data['value'];
			if(isset($data['field'])) {
				switch($data['field']) {
					case 'slugPath':
						if(isset($data["create"]) && $data["create"]) {
							foreach($terms as $term) {
								\Wprr\OddCore\Utils\TaxonomyFunctions::ensure_term($term, $data['taxonomy']);
							}
						}
						$terms = \Wprr\OddCore\Utils\TaxonomyFunctions::get_ids_from_terms(\Wprr\OddCore\Utils\TaxonomyFunctions::get_terms_by_slug_paths($terms, $data['taxonomy']));
				}
			}
			
			return $terms;
		}
		
		public function hook_set_terms($data, $post_id, $logger) {
			//echo("\Wprr\ChangePostHooks::hook_set_terms<br />");
			
			$terms = $this->get_terms($data);
			
			wp_set_post_terms($post_id, $terms, $data['taxonomy'], false);
		}
		
		public function hook_add_terms($data, $post_id, $logger) {
			//echo("\Wprr\ChangePostHooks::hook_add_terms<br />");
			
			$terms = $this->get_terms($data);
			
			wp_set_post_terms($post_id, $terms, $data['taxonomy'], true);
		}
		
		public function hook_remove_terms($data, $post_id, $logger) {
			$terms = $this->get_terms($data);
			
			wp_remove_object_terms($post_id, $terms, $data['taxonomy'], false);
		}
		
		public function hook_set_meta($data, $post_id, $logger) {
			//echo("\Wprr\ChangePostHooks::hook_set_meta<br />");
			
			$value = $data['value'];
			update_post_meta($post_id, $data['field'], $value);
		}
		
		public function hook_removeMeta($data, $post_id, $logger) {
			//echo("\Wprr\ChangePostHooks::hook_removeMeta<br />");
			
			if($data['field']) {
				delete_post_meta($post_id, $data['field']);
			}
			
		}
		
		public function hook_set_acf($data, $post_id, $logger) {
			//echo("\Wprr\ChangePostHooks::hook_set_acf<br />");
			
			$value = $data['value'];
			update_field($data['field'], $value, $post_id);
		}
		
		public function hook_wpml_create_duplicates($data, $post_id, $logger) {
			update_post_meta($post_id, '_wpml_media_featured', 1);
			do_action( 'wpml_admin_make_post_duplicates', $post_id );
		}
		
		public function hook_wpml_create_copies($data, $post_id, $logger) {
			update_post_meta($post_id, '_wpml_media_featured', 1);
			do_action( 'wpml_admin_make_post_duplicates', $post_id );
			
			$translations = apply_filters('wpml_post_duplicates', $post_id);
			
			foreach ($translations as $translation_id) {
				delete_post_meta($translation_id, '_icl_lang_duplicate_of', $post_id);
			}
		}
		
		public function hook_wpml_createLanguageCopy($data, $post_id, $logger) {
			global $sitepress;
			
			$language = $data['language'];
			
			if($language) {
				update_post_meta($post_id, '_wpml_media_featured', 1);
				$translation_id = $sitepress->make_duplicate($post_id, $language);
			
				delete_post_meta($translation_id, '_icl_lang_duplicate_of', $post_id);
			}
			else {
				$logger->add_log('No language set');
			}
		}
		
		public function hook_woocommerce_subscriptions_changeNextPayment($data, $post_id, $logger) {
			$subscription = new \WC_Subscription($post_id);
			
			$value = $data['value'];
			
			$time_zone = get_option('timezone_string');
			$result = $subscription->update_dates(array('next_payment' => $value), $time_zone);
		}
		
		public function hook_woocommerce_order_paymentMethod($data, $post_id, $logger) {
			$order = wc_get_order($post_id);
			
			$value = $data['value'];
			
			$order->set_payment_method($value);
			$order->save();
		}
		
		public function hook_woocommerce_order_addProduct($data, $post_id, $logger) {
			$order = wc_get_order($post_id);
			
			$value = $data['value'];
			$quantity = $data['quantity'] ? (int)$data['quantity'] : 1;
			
			$product = wc_get_product($value);
			$order->add_product($product, $quantity);
			
		}
		
		public function hook_woocommerce_order_calculateTotals($data, $post_id, $logger) {
			$order = wc_get_order($post_id);
			
			$order->calculate_totals();
			$order->save();
		}
		
		public static function test_import() {
			echo("Imported \Wprr\ChangePostHooks<br />");
		}
	}
?>