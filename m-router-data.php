<?php 
	/*
	Plugin Name: mRouter data
	Plugin URI: http://oddalice.se
	Description: Providing data for the mRouter
	Version: 0.2.2
	Author: Odd alice
	Author URI: http://oddalice.se
	*/
	
	define("M_ROUTER_DATA_DOMAIN", "m-rouuter-data");
	define("M_ROUTER_DATA_TEXTDOMAIN", "m-rouuter-data");
	define("M_ROUTER_DATA_MAIN_FILE", __FILE__);
	define("M_ROUTER_DATA_DIR", untrailingslashit( dirname( __FILE__ )  ) );
	define("M_ROUTER_DATA_URL", untrailingslashit( plugins_url('',  __FILE__ )  ) );
	define("M_ROUTER_DATA_VERSION", '0.2.2');
	
	function m_router_data_template_redirect() {
		if(isset($_GET['mRouterData']) && $_GET['mRouterData'] === 'json') {
			
			$debug = true;
			
			global $wp_query;
			
			$template_selection_parameters = array("is_single", "is_preview", "is_page", "is_archive", "is_date", "is_year", "is_month", "is_day", "is_time", "is_author", "is_category", "is_tag", "is_tax", "is_search", "is_feed", "is_comment_feed", "is_trackback", "is_home", "is_404", "is_embed", "is_paged", "is_admin", "is_attachment", "is_singular", "is_robots", "is_posts_page", "is_post_type_archive");
			
			$data = array();
			
			$data['m_router'] = array('version' => M_ROUTER_DATA_VERSION);
			
			if($debug) {
				$data['_queried_object'] = get_queried_object();
				$data['_query'] = $wp_query;
			}
			
			$posts = array();
			
			while(have_posts()) {
				the_post();
				
				$current_post_data = array();
				
				$post_id = get_the_ID();
				
				$current_post_data["ID"] = $post_id;
				$current_post_data["permalink"] = get_permalink();
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
			
			$data['posts'] = $posts;
			
			$template_selection = array();
			
			foreach($template_selection_parameters as $template_selection_parameter) {
				$template_selection[$template_selection_parameter] = $wp_query->$template_selection_parameter;
			}
			
			$data['templateSelection'] = $template_selection;
			
			header('Content-Type: application/json');
			header("Access-Control-Allow-Origin: *");
			echo(json_encode($data));
			
			exit();
		}
	}
	
	add_action('template_redirect', 'm_router_data_template_redirect');
?>