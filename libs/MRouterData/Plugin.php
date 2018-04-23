<?php
	namespace MRouterData;

	use \MRouterData\OddCore\PluginBase;

	class Plugin extends PluginBase {

		function __construct() {
			//echo("\MRouterData\Plugin::__construct<br />");

			$this->add_additional_hook(new \MRouterData\RedirectHooks());

			parent::__construct();

			//$this->add_javascript('m-router-data-main', M_ROUTER_DATA_URL.'/assets/js/main.js');
		}

		protected function create_pages() {
			//echo("\MRouterData\Plugin::create_pages<br />");

		}

		protected function create_custom_post_types() {
			//echo("\MRouterData\Plugin::create_custom_post_types<br />");



		}

		protected function create_additional_hooks() {
			//echo("\MRouterData\Plugin::create_additional_hooks<br />");


		}

		protected function create_rest_api_end_points() {
			//echo("\MRouterData\Plugin::create_rest_api_end_points<br />");

			$api_namespace = 'm-router-data';
			
			//METODO: add security
			//$this->create_rest_api_end_point(new \MRouterData\RestApi\EditPostEndPoint(), '(?P<post_type>[a-z0-9\-\_]+)/(?P<id>\d+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'), 'PUT'); // Update post
			//$this->create_rest_api_end_point(new \MRouterData\RestApi\CreateEditPostEndpoint(), 'post', $api_namespace, array('Access-Control-Allow-Origin' => '*'), 'POST'); // Create post
			
			//METODO: add security
			//$this->create_rest_api_end_point(new \MRouterData\RestApi\UploadAttachmentEndPoint(), 'attachment', $api_namespace, array('Access-Control-Allow-Origin' => '*'), 'POST');
			
			$this->create_rest_api_end_point(new \MRouterData\RestApi\GetSiteDataEndPoint(), 'site-data', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$this->create_rest_api_end_point(new \MRouterData\RestApi\PostDataByIdEndPoint(), 'post/(?P<id>\d+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$this->create_rest_api_end_point(new \MRouterData\RestApi\CommentsEndPoint(), 'post/(?P<id>\d+)/comments', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$this->create_rest_api_end_point(new \MRouterData\RestApi\SetInitialLoadForPostEndPoint(), 'post/(?P<id>\d+)/initial-load', $api_namespace, array('Access-Control-Allow-Origin' => '*'), 'POST');
			$this->create_rest_api_end_point(new \MRouterData\RestApi\SetSeoRenderEndPoint(), 'seo-render', $api_namespace, array('Access-Control-Allow-Origin' => '*'), 'POST');

			//$this->create_rest_api_end_point(new \MRouterData\RestApi\SetMetadataEndpoint(), 'metadata', $api_namespace, array('Access-Control-Allow-Origin' => '*'), 'POST');

			$this->create_rest_api_end_point(new \MRouterData\RestApi\CustomizerData(), 'customizer/(?P<options>[a-z0-9\,\[\]\-\_\,]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \MRouterData\RestApi\PostRangeEndPoint(), 'post-range/(?P<post_type>[a-z0-9\-\_,]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \MRouterData\RestApi\ImageRangeEndPoint(), 'image-range/(?P<post_type>[a-z0-9\-\_]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));

			$this->create_rest_api_end_point(new \MRouterData\RestApi\CustomRangeEndPoint(), 'custom-range/(?P<range_type>[a-z0-9\-\_]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \MRouterData\RestApi\CustomItemEndpoint(), 'custom-item/(?P<item_type>[a-z0-9\-\_]+)/(?P<id>.+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			
			$this->create_rest_api_end_point(new \MRouterData\RestApi\AcfOptionsEndPoint(), 'acf-options', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \MRouterData\RestApi\GetMenuEndPoint(), 'menu/(?P<location>[a-z0-9\-\_]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$this->create_rest_api_end_point(new \MRouterData\RestApi\GetTermsEndPoint(), 'taxonomy/(?P<taxonomy>[a-z0-9\-\_]+)/terms', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \MRouterData\RestApi\GetTaxonomiesEndPoint(), 'taxonomies', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$current_end_point = new \MRouterData\RestApi\AddTermToPostEndPoint();
			$current_end_point->add_headers(array('Access-Control-Allow-Origin' => '*'));
			$current_end_point->setup('post/(?P<post_id>\d+)/(?P<taxonomy>[a-z0-9\-\_]+)/terms/add', $api_namespace, 1, 'POST');
			$current_end_point->set_requiered_capability('edit_others_posts');
			$this->_rest_api_end_points[] = $current_end_point;
			
			$current_end_point = new \MRouterData\RestApi\RemoveTermFromPostEndPoint();
			$current_end_point->add_headers(array('Access-Control-Allow-Origin' => '*'));
			$current_end_point->setup('post/(?P<post_id>\d+)/(?P<taxonomy>[a-z0-9\-\_]+)/terms/remove', $api_namespace, 1, 'POST');
			$current_end_point->set_requiered_capability('edit_others_posts');
			$this->_rest_api_end_points[] = $current_end_point;
		}
		
		public function filter_id_check_for_has_permission($has_permission) {
			//echo("\MRouterData\Plugin::filter_id_check_for_has_permission<br />");
			
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
			
			$encoder = new \MRouterData\MRouterDataEncoder();
			
			$user_data['data'] = $encoder->encode_user_with_private_data($user);
			$user_data['roles'] = $user->roles;
			$user_data['restNonce'] = wp_create_nonce('wp_rest');
			
			return $user_data;
		}
		
		protected function create_filters() {
			//echo("\MRouterData\Plugin::create_filters<br />");
			
			add_filter(M_ROUTER_DATA_DOMAIN.'/'.'has_permission', array($this, 'filter_id_check_for_has_permission'), 10, 1);
			add_filter(M_ROUTER_DATA_DOMAIN.'/'.'configuration_image_sizes', array($this, 'filter_image_sizes'), 10, 1);
			add_filter(M_ROUTER_DATA_DOMAIN.'/'.'configuration_paths', array($this, 'filter_paths'), 10, 1);
			add_filter(M_ROUTER_DATA_DOMAIN.'/'.'configuration_user_data', array($this, 'filter_user_data'), 10, 3);
			add_filter(M_ROUTER_DATA_DOMAIN.'/'.'configuration_user_data_if_logged_in', array($this, 'filter_user_data_if_logged_in'), 10, 1);
			
			add_action(M_ROUTER_DATA_DOMAIN.'/'.'prepare_api_request', array($this, 'hook_prepare_api_request'), 10, 1);
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
			//echo("\MRouterData\Plugin::hook_admin_enqueue_scripts<br />");

			parent::hook_admin_enqueue_scripts();

			/*
			$screen = get_current_screen();

			wp_enqueue_script( 'dijoy-wine-products-importer-admin-main', DIJOY_WINE_PRODUCTS_IMPORTER_URL . '/assets/js/admin-main.js');
			wp_localize_script(
				'dijoy-wine-products-importer',
				'oaWpAdminData_MRouterData',
				array(
					'screen' => $screen,
					'restApiBaseUrl' => get_home_url().'/wp-json/'
				)
			);
			*/
		}



		public static function test_import() {
			echo("Imported \MRouterData\Plugin<br />");
		}
	}
?>
