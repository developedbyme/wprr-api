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
		
		protected function register_hook_for_type($type, $hook_name) {
			
		}
		
		public function register() {
			//echo("\Wprr\RangeHooks::register<br />");
			
			add_filter(WPRR_DOMAIN.'/range_query/standard', array($this, 'filter_query_standard'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/default', array($this, 'filter_query_standard'), 10, 2);
			//add_filter(WPRR_DOMAIN.'/range_selection_has_permission/drafts', array('\Wprr\PermissionFilters', 'waterfall_is_admin'), 10, 1);
			add_filter(WPRR_DOMAIN.'/range_query/drafts', array($this, 'filter_query_drafts'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/attachmentStatus', array($this, 'filter_query_attachment_status'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/idSelection', array($this, 'filter_query_id_selection'), 10, 2);
			add_filter(WPRR_DOMAIN.'/range_query/inTaxonomy', array($this, 'filter_query_in_taxonomy'), 10, 2);
			
			add_filter(WPRR_DOMAIN.'/range_encoding/id', array($this, 'filter_encode_id'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/standard', array($this, 'filter_encode_standard'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/default', array($this, 'filter_encode_standard'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/status', array($this, 'filter_encode_status'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/translations', array($this, 'filter_encode_translations'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/attachment', array($this, 'filter_encode_attachment'), 10, 3);
			
			add_filter(WPRR_DOMAIN.'/range_encoding/editFields', array($this, 'filter_encode_standard'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/editFields', array($this, 'filter_encode_edit_fields'), 10, 3);
			add_filter(WPRR_DOMAIN.'/range_encoding/editFields', array($this, 'filter_encode_status'), 10, 3);
			
			add_filter(WPRR_DOMAIN.'/range_encoding/fullPost', array($this, 'filter_encode_full_post'), 10, 3);
		}
		
		public function filter_query_standard($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_standard<br />");
			
			//MENOTE: do nothing
			
			return $query_args;
		}
		
		public function filter_query_id_selection($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_id_selection<br />");
			
			$query_args['post__in'] = explode(',', $data['ids']);
			
			return $query_args;
		}
		
		public function filter_query_in_taxonomy($query_args, $data) {
			//echo("\Wprr\RangeHooks::filter_query_in_taxonomy<br />");
			
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
			
			if(!isset($query_args['post_status'])) {
				$query_args['post_status'] = array('publish');
			}
			
			$query_args['post_status'][] = 'draft';
			$query_args['post_status'][] = 'pending';
			
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
		
		public function filter_encode_full_post($encoded_data, $post_id, $data) {
			//echo("\Wprr\RangeHooks::filter_encode_full_post<br />");
			
			$encoded_data = mrouter_encode_post(get_post($post_id));
			
			return $encoded_data;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\RangeHooks<br />");
		}
	}
?>