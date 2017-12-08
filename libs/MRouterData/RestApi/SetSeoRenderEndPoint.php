<?php
	namespace MRouterData\RestApi;

	use \WP_Query;
	use \MRouterData\OddCore\RestApi\EndPoint as EndPoint;

	// \MRouterData\RestApi\SetSeoRenderEndPoint
	class SetSeoRenderEndPoint extends EndPoint {

		function __construct() {
			// echo("\OddCore\RestApi\SetSeoRenderEndPoint::__construct<br />");
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
			// echo("\OddCore\RestApi\SetSeoRenderEndPoint::perform_call<br />");

			$path = $data['path'];
			$key = $data['key'];
			$seo_render = $data['seoRender'];
			
			$upload_dir = wp_upload_dir(null, false);
			
			$salt = apply_filters('m_router_data/salt', 'wvIUIAULTxKicDpbkzyPpVi5wskSe6Yxy0Uq4wCqbAui1wVKAKmsVhN7JOhGbFQohVs9pnpQoS1dWGkL');
			$render_key_salt = apply_filters('m_router_data/render_key_salt', 'DsHWtvGPGje5kjDetOVWd2CkflKWztdDRAMA7FN4b9tbqkXfozxH0ET7dbB92wRdNZOVBuVUZQWiRiqP');
			
			$generated_key = md5($path.$render_key_salt);
			
			if($generated_key !== $key) {
				return $this->output_error("Key is incorrect");
			}
			
			$upload_path = $upload_dir['basedir'].'/mrouter-seo-renders/'.md5($generated_key.$salt).'.html';
			$saved = $this->create_folders_and_save_file($upload_path, $seo_render);
			
			return $this->output_success(null);
		}

		public static function test_import() {
			echo("Imported \OddCore\RestApi\SetSeoRenderEndPoint<br />");
		}
	}
