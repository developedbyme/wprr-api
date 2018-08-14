<?php
	namespace Wprr;
	
	use \WP_Query;
	
	// \Wprr\RangeHooks
	class RangeHooks {
		
		function __construct() {
			//echo("\Wprr\RangeHooks::__construct<br />");
			
			
		}
		
		protected function register_hook_for_type($type, $hook_name) {
			
		}
		
		public function register() {
			//echo("\Wprr\RangeHooks::register<br />");
			
			add_filter(WPRR_DOMAIN.'/range_query/standard', array($this, 'filter_query_standard'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/drafts', array($this, 'filter_query_drafts'), 10, 2);
			
			add_filter(WPRR_DOMAIN.'/range_encoding/id', array($this, 'filter_encode_id'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/standard', array($this, 'filter_encode_standard'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/status', array($this, 'filter_encode_status'), 10, 3);
			
		}
		
		public function filter_query_standard($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_standard<br />");
			
			//MENOTE: do nothing
			
			return $query_args;
		}
		
		public function filter_query_drafts($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_drafts<br />");
			
			if(!isset($query_args['post_status'])) {
				$query_args['post_status'] = array('publish');
			}
			
			$query_args['post_status'][] = 'draft';
			$query_args['post_status'][] = 'pending';
			
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
		
		public function filter_encode_status($encoded_data, $post_id, $data) {
			//echo("\Wprr\RangeHooks::filter_encode_status<br />");
			
			$encoded_data['status'] = get_post_status($post_id);
			
			return $encoded_data;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\RangeHooks<br />");
		}
	}
?>