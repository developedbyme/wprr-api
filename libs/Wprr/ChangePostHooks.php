<?php
	namespace Wprr;
	
	use \WP_Query;
	
	// \Wprr\ChangePostHooks
	class ChangePostHooks {
		
		function __construct() {
			//echo("\Wprr\ChangePostHooks::__construct<br />");
			
			
		}
		
		protected function register_hook_for_type($type, $hook_name) {
			add_action(WPRR_DOMAIN.'/admin/change_post/'.$type, array($this, $hook_name), 10, 2);
		}
		
		public function register() {
			//echo("\Wprr\ChangePostHooks::register<br />");
			
			$this->register_hook_for_type('parent', 'hook_set_parent');
			$this->register_hook_for_type('resave', 'hook_resave');
			$this->register_hook_for_type('status', 'hook_set_status');
			$this->register_hook_for_type('content', 'hook_set_content');
			$this->register_hook_for_type('slug', 'hook_set_slug');
			$this->register_hook_for_type('title', 'hook_set_title');
			$this->register_hook_for_type('terms', 'hook_set_terms');
			$this->register_hook_for_type('addTerms', 'hook_add_terms');
			//METODO: remove terms
			$this->register_hook_for_type('meta', 'hook_set_meta');
			//METODO: remove meta
			$this->register_hook_for_type('acf', 'hook_set_acf');
			
		}
		
		protected function update_post_data($post_id, $field, $value) {
			return wp_update_post(array(
				'ID' => $post_id,
				$field => $value
			));
		}
		
		public function hook_set_parent($data, $post_id) {
			//echo("\Wprr\ChangePostHooks::hook_set_parent<br />");
			
			$this->update_post_data($post_id, 'post_parent', $data['value']);
		}
		
		public function hook_resave($data, $post_id) {
			//echo("\Wprr\ChangePostHooks::hook_resave<br />");
			
			wp_update_post(array(
				'ID' => $post_id
			));
		}
		
		public function hook_set_status($data, $post_id) {
			//echo("\Wprr\ChangePostHooks::hook_set_status<br />");
			
			$this->update_post_data($post_id, 'post_status', $data['value']);
		}
		
		public function hook_set_content($data, $post_id) {
			//echo("\Wprr\ChangePostHooks::hook_set_content<br />");
			
			$this->update_post_data($post_id, 'post_content', $data['value']);
		}
		
		public function hook_set_slug($data, $post_id) {
			//echo("\Wprr\ChangePostHooks::hook_set_content<br />");
			
			$this->update_post_data($post_id, 'post_name', $data['value']);
		}
		
		public function hook_set_title($data, $post_id) {
			//echo("\Wprr\ChangePostHooks::hook_set_title<br />");
			
			$this->update_post_data($post_id, 'post_title', $data['value']);
		}
		
		protected function get_terms($data) {
			$terms = $data['value'];
			if(isset($data['field'])) {
				switch($data['field']) {
					case 'slugPath':
						$terms = \Wprr\OddCore\Utils\TaxonomyFunctions::get_ids_from_terms(\Wprr\OddCore\Utils\TaxonomyFunctions::get_terms_by_slug_paths($terms, $data['taxonomy']));
				}
			}
			
			return $terms;
		}
		
		public function hook_set_terms($data, $post_id) {
			//echo("\Wprr\ChangePostHooks::hook_set_terms<br />");
			
			$terms = $this->get_terms($data);
			
			wp_set_post_terms($post_id, $terms, $data['taxonomy'], false);
		}
		
		public function hook_add_terms($data, $post_id) {
			//echo("\Wprr\ChangePostHooks::hook_add_terms<br />");
			
			$terms = $this->get_terms($data);
			
			wp_set_post_terms($post_id, $terms, $data['taxonomy'], true);
		}
		
		public function hook_set_meta($data, $post_id) {
			//echo("\Wprr\ChangePostHooks::hook_set_meta<br />");
			
			$value = $data['value'];
			update_post_meta($post_id, $data['field'], $value);
		}
		
		public function hook_set_acf($data, $post_id) {
			//echo("\Wprr\ChangePostHooks::hook_set_acf<br />");
			
			$value = $data['value'];
			update_field($data['field'], $value, $post_id);
		}
		
		public static function test_import() {
			echo("Imported \Wprr\ChangePostHooks<br />");
		}
	}
?>