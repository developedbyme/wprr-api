<?php
	namespace MRouterData\RestApi;

	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;

	// \MRouterData\RestApi\SetInitialLoadForPostEndPoint
	class SetInitialLoadForPostEndPoint extends EndPoint {

		function __construct() {
			// echo("\OddCore\RestApi\SetInitialLoadForPostEndPoint::__construct<br />");
		}
		
		protected function create_folders_and_save_file($full_path, $content) {
			
			$parts = explode('/', $full_path);
			$file = array_pop($parts);
			$dir = '';
			
			foreach($parts as $part) {
				if(!is_dir($dir .= "/$part")) {
					mkdir($dir);
				}
			}
			
			return file_put_contents($full_path, $content);
		}

		public function perform_call($data) {
			// echo("\OddCore\RestApi\SetInitialLoadForPostEndPoint::perform_call<br />");

			$post_id = $data['id'];
			$paths = $data['paths'];
			$permalink = $data['permalink'];
			$seo_render = $data['seoRender'];

			$meta_data = update_post_meta($post_id, 'mrouter_initital_load', $paths);
			
			$upload_dir = wp_upload_dir(null, false);
			
			$salt = apply_filters('m_router_data/salt', 'wvIUIAULTxKicDpbkzyPpVi5wskSe6Yxy0Uq4wCqbAui1wVKAKmsVhN7JOhGbFQohVs9pnpQoS1dWGkL');
			
			$upload_path = $upload_dir['basedir'].'/mrouter-seo-renders/'.md5($permalink.$salt).'.html';
			$saved = $this->create_folders_and_save_file($upload_path, $seo_render);
			if($saved) {
				update_post_meta($post_id, 'mrouter_has_seo_render', 1);
			} 
			
			
			//METODO: check diffs

			if(!$meta_data) {
				return $this->output_error('Metadata not edited/created');
			}
			
			//METODO: clear cache

			return $this->output_success($meta_data);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\SetInitialLoadForPostEndPoint<br />");
		}
	}
