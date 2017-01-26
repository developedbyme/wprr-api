<?php
	namespace MRouterData;
	
	use \WP_Query;
	
	// \MRouterData\RedirectHooks
	class RedirectHooks {
		
		protected $settings = null;
		
		function __construct() {
			//echo("\MRouterData\RedirectHooks::__construct<br />");
			
			
		}
		
		public function register() {
			//echo("\MRouterData\RedirectHooks::register<br />");
			
			add_action('template_redirect', array($this, 'hook_template_redirect'));
			
		}
		
		
		
		public function hook_template_redirect() {
			//echo("\MRouterData\RedirectHooks::hook_template_redirect<br />");
			
			if(isset($_GET['mRouterData']) && $_GET['mRouterData'] === 'json') {
			
				$debug = true;
			
				global $wp_query;
			
				$template_selection_parameters = array("is_single", "is_preview", "is_page", "is_archive", "is_date", "is_year", "is_month", "is_day", "is_time", "is_author", "is_category", "is_tag", "is_tax", "is_search", "is_feed", "is_comment_feed", "is_trackback", "is_home", "is_404", "is_embed", "is_paged", "is_admin", "is_attachment", "is_singular", "is_robots", "is_posts_page", "is_post_type_archive");
			
				$data = array();
				$data['metadata'] = array();
				$data['data'] = array();
			
				$data['metadata']['mRouter'] = array('version' => M_ROUTER_DATA_VERSION);
			
				$queried_object = get_queried_object();
			
				if($debug) {
					$data['data']['_queried_object'] = $queried_object;
					$data['data']['_query'] = $wp_query;
				}
			
				$posts = array();
			
				while(have_posts()) {
					the_post();
				
					$current_post_data = array();
				
					$post = get_post();
					$post_id = get_the_ID();
				
					$current_post_data["id"] = $post_id;
					$current_post_data["type"] = $post->post_type;
					$current_post_data["status"] = $post->post_status;
					$current_post_data["permalink"] = get_permalink();
					$current_post_data["publishedDate"] = $post->post_date;
					$current_post_data["modifiedDate"] = $post->post_modified;
					$current_post_data["title"] = get_the_title();
					$current_post_data["excerpt"] = apply_filters('the_excerpt', get_the_excerpt());
					$current_post_data["content"] = apply_filters('the_content', get_the_content());
					$current_post_data["meta"] = get_post_meta($post_id);
				
					$taxonomies = array_keys(get_the_taxonomies($post_id));
					$term_data_array = array();
					foreach($taxonomies as $taxonomy) {
						$term_data_array[$taxonomy] = get_the_terms($post_id, $taxonomy);
					}
					$current_post_data["terms"] = $term_data_array;
				
					$posts[] = $current_post_data;
				}
			
				$data['data']['posts'] = $posts;
			
				$template_selection = array();
			
				foreach($template_selection_parameters as $template_selection_parameter) {
					$template_selection[$template_selection_parameter] = $wp_query->$template_selection_parameter;
				}
			
				if(is_singular()) {
					$template_selection['post_type'] = $queried_object->post_type;
				}
				else {
					$template_selection['post_type'] = null;
				}
			
				$data['data']['templateSelection'] = $template_selection;
			
				header('Content-Type: application/json');
				header("Access-Control-Allow-Origin: *");
				echo(json_encode($data));
			
				exit();
			}
		}
		
		public static function test_import() {
			echo("Imported \MRouterData\RedirectHooks<br />");
		}
	}
?>