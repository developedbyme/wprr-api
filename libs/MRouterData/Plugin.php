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

			$this->create_rest_api_end_point(new \MRouterData\RestApi\PostDataByIdEndPoint(), 'post/(?P<id>\d+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \MRouterData\RestApi\CustomizerData(), 'customizer/(?P<options>[a-z0-9\,\[\]\-\_\,]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \MRouterData\RestApi\PostRangeEndPoint(), 'post-range/(?P<post_type>[a-z0-9\-\_]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			$this->create_rest_api_end_point(new \MRouterData\RestApi\ImageRangeEndPoint(), 'image-range/(?P<post_type>[a-z0-9\-\_]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));

			$this->create_rest_api_end_point(new \MRouterData\RestApi\CustomRangeEndPoint(), 'custom-range/(?P<range_type>[a-z0-9\-\_]+)', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			
			$this->create_rest_api_end_point(new \MRouterData\RestApi\PostCaseEndPoint(), 'post-case', $api_namespace, array('Access-Control-Allow-Origin' => '*'));
			
			
			

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
