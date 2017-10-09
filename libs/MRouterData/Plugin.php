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
			
			$this->create_rest_api_end_point(new \MRouterData\RestApi\PostDataByIdEndPoint(), 'post/(?P<id>\d+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$this->create_rest_api_end_point(new \MRouterData\RestApi\CommentsEndPoint(), 'post/(?P<id>\d+)/comments', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$this->create_rest_api_end_point(new \MRouterData\RestApi\SetInitialLoadForPostEndPoint(), 'post/(?P<id>\d+)/initial-load', $api_namespace, array('Access-Control-Allow-Origin' => '*'), 'POST');

			//$this->create_rest_api_end_point(new \MRouterData\RestApi\SetMetadataEndpoint(), 'metadata', $api_namespace, array('Access-Control-Allow-Origin' => '*'), 'POST');

			$this->create_rest_api_end_point(new \MRouterData\RestApi\CustomizerData(), 'customizer/(?P<options>[a-z0-9\,\[\]\-\_\,]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \MRouterData\RestApi\PostRangeEndPoint(), 'post-range/(?P<post_type>[a-z0-9\-\_]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \MRouterData\RestApi\ImageRangeEndPoint(), 'image-range/(?P<post_type>[a-z0-9\-\_]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));

			$this->create_rest_api_end_point(new \MRouterData\RestApi\CustomRangeEndPoint(), 'custom-range/(?P<range_type>[a-z0-9\-\_]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$this->create_rest_api_end_point(new \MRouterData\RestApi\AcfOptionsEndPoint(), 'acf-options', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \MRouterData\RestApi\GetMenuEndPoint(), 'menu/(?P<location>[a-z0-9\-\_]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			$this->create_rest_api_end_point(new \MRouterData\RestApi\GetTermsEndPoint(), 'taxonomy/(?P<taxonomy>[a-z0-9\-\_]+)/terms', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			

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
		
		protected function create_filters() {
			//echo("\MRouterData\Plugin::create_filters<br />");
			
			add_filter(M_ROUTER_DATA_DOMAIN.'/'.'has_permission', array($this, 'filter_id_check_for_has_permission'), 10, 1);
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
