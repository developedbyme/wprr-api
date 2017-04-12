<?php
	namespace MRouterData;

	use \WP_Query;
	use \WP_Term;
	use \WP_Post;
	use \WP_User;

	// \MRouterData\RedirectHooks
	class RedirectHooks {

		protected $settings = null;
		
		protected $encoder = null;

		function __construct() {
			//echo("\MRouterData\RedirectHooks::__construct<br />");
			
			$this->encoder = new \MRouterData\MRouterDataEncoder();
			
		}

		public function register() {
			//echo("\MRouterData\RedirectHooks::register<br />");

			add_action('template_redirect', array($this, 'hook_template_redirect'));

		}

		public function hook_template_redirect() {
			//echo("\MRouterData\RedirectHooks::hook_template_redirect<br />");

			if(isset($_GET['mRouterData']) && $_GET['mRouterData'] === 'json') {
				
				$permission_filter_name = M_ROUTER_DATA_DOMAIN.'/'.'has_permission'; 
			
				//if(has_filter($permission_filter_name)) {
					
					$has_permissions = apply_filters($permission_filter_name, true);
					
					if(!$has_permissions) {
						header('HTTP/1.0 403 Forbidden');
						header('Content-Type: application/json');
						header("Access-Control-Allow-Origin: *");
						
						$data = array('status' => 'error', 'message' => 'Access denied');
						
						echo(json_encode($data));
						
						exit();
					}
					//}
				
				$data = $this->encoder->encode();
				
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
