<?php
	namespace Wprr;

	use \Wprr\OddCore\PluginBase;

	class Plugin extends PluginBase {

		function __construct() {
			//echo("\Wprr\Plugin::__construct<br />");

			$this->add_additional_hook(new \Wprr\RedirectHooks());

			parent::__construct();

			//$this->add_javascript('m-router-data-main', WPRR_URL.'/assets/js/main.js');
		}

		protected function create_pages() {
			//echo("\Wprr\Plugin::create_pages<br />");

		}

		protected function create_custom_post_types() {
			//echo("\Wprr\Plugin::create_custom_post_types<br />");



		}

		protected function create_additional_hooks() {
			//echo("\Wprr\Plugin::create_additional_hooks<br />");


		}

		protected function create_rest_api_end_points() {
			//echo("\Wprr\Plugin::create_rest_api_end_points<br />");

			$api_namespace = 'm-router-data';
			
			//METODO: add security
			//$this->create_rest_api_end_point(new \Wprr\RestApi\EditPostEndPoint(), '(?P<post_type>[a-z0-9\-\_]+)/(?P<id>\d+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'), 'PUT'); // Update post
			//$this->create_rest_api_end_point(new \Wprr\RestApi\CreateEditPostEndpoint(), 'post', $api_namespace, array('Access-Control-Allow-Origin' => '*'), 'POST'); // Create post
			
			//METODO: add security
			//$this->create_rest_api_end_point(new \Wprr\RestApi\UploadAttachmentEndPoint(), 'attachment', $api_namespace, array('Access-Control-Allow-Origin' => '*'), 'POST');
			
			$this->create_rest_api_end_point(new \Wprr\RestApi\GetSiteDataEndPoint(), 'site-data', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$this->create_rest_api_end_point(new \Wprr\RestApi\PostDataByIdEndPoint(), 'post/(?P<id>\d+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$this->create_rest_api_end_point(new \Wprr\RestApi\CommentsEndPoint(), 'post/(?P<id>\d+)/comments', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$this->create_rest_api_end_point(new \Wprr\RestApi\SetInitialLoadForPostEndPoint(), 'post/(?P<id>\d+)/initial-load', $api_namespace, array('Access-Control-Allow-Origin' => '*'), 'POST');
			$this->create_rest_api_end_point(new \Wprr\RestApi\SetSeoRenderEndPoint(), 'seo-render', $api_namespace, array('Access-Control-Allow-Origin' => '*'), 'POST');

			//$this->create_rest_api_end_point(new \Wprr\RestApi\SetMetadataEndpoint(), 'metadata', $api_namespace, array('Access-Control-Allow-Origin' => '*'), 'POST');

			$this->create_rest_api_end_point(new \Wprr\RestApi\CustomizerData(), 'customizer/(?P<options>[a-z0-9\,\[\]\-\_\,]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \Wprr\RestApi\PostRangeEndPoint(), 'post-range/(?P<post_type>[a-z0-9\-\_,]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \Wprr\RestApi\ImageRangeEndPoint(), 'image-range/(?P<post_type>[a-z0-9\-\_]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));

			$this->create_rest_api_end_point(new \Wprr\RestApi\CustomRangeEndPoint(), 'custom-range/(?P<range_type>[a-z0-9\-\_]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \Wprr\RestApi\CustomItemEndpoint(), 'custom-item/(?P<item_type>[a-z0-9\-\_]+)/(?P<id>.+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			
			$this->create_rest_api_end_point(new \Wprr\RestApi\AcfOptionsEndPoint(), 'acf-options', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \Wprr\RestApi\GetMenuEndPoint(), 'menu/(?P<location>[a-z0-9\-\_]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$this->create_rest_api_end_point(new \Wprr\RestApi\GetTermsEndPoint(), 'taxonomy/(?P<taxonomy>[a-z0-9\-\_]+)/terms', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \Wprr\RestApi\GetTaxonomiesEndPoint(), 'taxonomies', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$current_end_point = new \Wprr\RestApi\AddTermToPostEndPoint();
			$current_end_point->add_headers(array('Access-Control-Allow-Origin' => '*'));
			$current_end_point->setup('post/(?P<post_id>\d+)/(?P<taxonomy>[a-z0-9\-\_]+)/terms/add', $api_namespace, 1, 'POST');
			$current_end_point->set_requiered_capability('edit_others_posts');
			$this->_rest_api_end_points[] = $current_end_point;
			
			$current_end_point = new \Wprr\RestApi\RemoveTermFromPostEndPoint();
			$current_end_point->add_headers(array('Access-Control-Allow-Origin' => '*'));
			$current_end_point->setup('post/(?P<post_id>\d+)/(?P<taxonomy>[a-z0-9\-\_]+)/terms/remove', $api_namespace, 1, 'POST');
			$current_end_point->set_requiered_capability('edit_others_posts');
			$this->_rest_api_end_points[] = $current_end_point;
			
			
			$api_namespace = 'wprr';
			
			$this->create_rest_api_end_point(new \Wprr\RestApi\RangeEndpoint(), 'range/(?P<post_types>[a-z0-9\-\_,]+)/(?P<selections>[a-z0-9\-\_,]+)/(?P<encodings>[a-z0-9\-\_,]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
		}
		
		public function filter_id_check_for_has_permission($has_permission) {
			//echo("\Wprr\Plugin::filter_id_check_for_has_permission<br />");
			
			if(!$has_permission) {
				return $has_permission;
			}
			
			$has_permission_filter_name = M_ROUTER_DATA_DOMAIN.'/id_has_permission';
			
			if(is_singular()) {
				if(have_posts()) {
					the_post();
					$post = get_post();
					
					$has_permission = apply_filters($has_permission_filter_name, $has_permission, $post->ID);
				}
				rewind_posts();
			}
			
			return $has_permission;
		}
		
		public function filter_paths($paths) {
			
			$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			
			$paths['current'] = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$paths['home'] = get_home_url();
			$paths['site'] = get_site_url();
			$paths['theme'] = get_stylesheet_directory_uri();
			$paths['rest'] = rest_url();
			
			$paths['login'] = wp_login_url();
			$paths['logout'] = wp_logout_url();
			$paths['lostPassword'] = wp_lostpassword_url();
			
			return $paths;
		}
		
		public function filter_image_sizes($image_sizes) {
			
			global $_wp_additional_image_sizes;
			
			foreach(get_intermediate_image_sizes() as $current_size) {
				
				if(in_array($current_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
					$image_sizes[$current_size]['width'] = get_option( "{$current_size}_size_w");
					$image_sizes[$current_size]['height'] = get_option( "{$current_size}_size_h");
					$image_sizes[$current_size]['crop'] = (bool) get_option( "{$current_size}_crop");
				}
				else if(isset($_wp_additional_image_sizes[$current_size])) {
					$image_sizes[$current_size] = array(
						'width' => $_wp_additional_image_sizes[$current_size]['width'],
						'height' => $_wp_additional_image_sizes[$current_size]['height'],
						'crop' => $_wp_additional_image_sizes[$current_size]['crop'],
					);
				}
			}
			$image_sizes['full'] = array(
				'width' => 0,
				'height' => 0,
				'crop' => false,
			);
			
			
			return $image_sizes;
		}
		
		public function filter_user_data_if_logged_in($null_value) {
			if(is_user_logged_in()) {
				
				$current_user = wp_get_current_user();
				
				return apply_filters(M_ROUTER_DATA_DOMAIN.'/'.'configuration_user_data', array(), $current_user->ID, $current_user);
			}
			
			return $null_value;
		}
		
		public function filter_user_data($user_data, $user_id, $user) {
			
			$encoder = new \Wprr\WprrEncoder();
			
			$user_data['data'] = $encoder->encode_user_with_private_data($user);
			$user_data['roles'] = $user->roles;
			$user_data['restNonce'] = wp_create_nonce('wp_rest');
			
			return $user_data;
		}
		
		public function filter_admin_data($admin_data) {
			
			if(function_exists('get_current_screen')) {
				$screen = get_current_screen();
				
				$admin_data['screen'] = $screen;
				
				if($screen->parent_base === 'edit') {
					global $post;
					$admin_data['post'] = mrouter_encode_post($post);
				}
			}
			
			return $admin_data;
		}
		
		public function filter_acf_post_meta($meta_data, $post_id) {
			
			//METODO: move this to other function
			unset($meta_data['_last_edit']);
			unset($meta_data['_edit_lock']);
			
			$encoder = new \Wprr\WprrEncoder();
			
			if(function_exists('get_field_objects')) {
				$fields_object = get_field_objects($post_id, false, true);
				
				if($fields_object !== false) {
					foreach($fields_object as $name => $field_object) {
						$unencoded_value = $field_object['value'];
						$encoded_value = $encoder->encode_acf_value($unencoded_value, $field_object, $post_id);
						
						$meta_data[$name] = $encoded_value;
						unset($meta_data['_'.$name]);
					}
				}
			}
			
			return $meta_data;
		}
		
		protected function create_filters() {
			//echo("\Wprr\Plugin::create_filters<br />");
			
			add_filter(M_ROUTER_DATA_DOMAIN.'/'.'has_permission', array($this, 'filter_id_check_for_has_permission'), 10, 1);
			add_filter(M_ROUTER_DATA_DOMAIN.'/'.'configuration_image_sizes', array($this, 'filter_image_sizes'), 10, 1);
			add_filter(M_ROUTER_DATA_DOMAIN.'/'.'configuration_paths', array($this, 'filter_paths'), 10, 1);
			add_filter(M_ROUTER_DATA_DOMAIN.'/'.'configuration_user_data', array($this, 'filter_user_data'), 10, 3);
			add_filter(M_ROUTER_DATA_DOMAIN.'/'.'configuration_user_data_if_logged_in', array($this, 'filter_user_data_if_logged_in'), 10, 1);
			add_filter(M_ROUTER_DATA_DOMAIN.'/'.'configuration_admin_data', array($this, 'filter_admin_data'), 10, 3);
			
			add_action(M_ROUTER_DATA_DOMAIN.'/'.'prepare_api_request', array($this, 'hook_prepare_api_request'), 10, 1);
			
			add_action(M_ROUTER_DATA_DOMAIN.'/'.'filter_post_meta', array($this, 'filter_acf_post_meta'), 10, 2);
		}
		
		public function hook_prepare_api_request($data) {
			if(isset($data['language'])) {
				global $sitepress;
	
				if(isset($sitepress)) {
					$sitepress->switch_lang($data['language']);
				}
				
				if(function_exists('acf_update_setting')) {
					acf_update_setting('current_language', $data['language']);
				}
			}
		}

		public function hook_admin_enqueue_scripts() {
			//echo("\Wprr\Plugin::hook_admin_enqueue_scripts<br />");

			parent::hook_admin_enqueue_scripts();

			/*
			$screen = get_current_screen();

			wp_enqueue_script( 'dijoy-wine-products-importer-admin-main', DIJOY_WINE_PRODUCTS_IMPORTER_URL . '/assets/js/admin-main.js');
			wp_localize_script(
				'dijoy-wine-products-importer',
				'oaWpAdminData_Wprr',
				array(
					'screen' => $screen,
					'restApiBaseUrl' => get_home_url().'/wp-json/'
				)
			);
			*/
		}



		public static function test_import() {
			echo("Imported \Wprr\Plugin<br />");
		}
	}
?>