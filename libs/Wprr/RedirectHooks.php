<?php
	namespace Wprr;

	use \WP_Query;
	use \WP_Term;
	use \WP_Post;
	use \WP_User;

	// \Wprr\RedirectHooks
	class RedirectHooks {

		protected $settings = null;
		
		protected $encoder = null;

		function __construct() {
			//echo("\Wprr\RedirectHooks::__construct<br />");
			
			$this->encoder = new \Wprr\WprrEncoder();
			
		}

		public function register() {
			//echo("\Wprr\RedirectHooks::register<br />");

			add_action('template_redirect', array($this, 'hook_template_redirect'));

		}

		public function hook_template_redirect() {
			//echo("\Wprr\RedirectHooks::hook_template_redirect<br />");

			if(isset($_GET['mRouterData']) && $_GET['mRouterData'] === 'json') {
				
				$permission_filter_name = M_ROUTER_DATA_DOMAIN.'/'.'has_permission'; 
			
				//if(has_filter($permission_filter_name)) {
					
					$has_permissions = apply_filters($permission_filter_name, true);
					
					if(!$has_permissions) {
						header('HTTP/1.0 403 Forbidden');
						header('Content-Type: application/json');
						header("Access-Control-Allow-Origin: *");
						
						$data = array('code' => 'error', 'message' => 'Access denied');
						
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
			
			$url = strtok($_SERVER["REQUEST_URI"], '?');
			
			if(( strpos($url, '/id-redirect/') === 0 ) ) {
				
				$id = intval($_GET['id']);
				$post = get_post($id);
		
				if($post) {
					wp_redirect(get_permalink($id));
		
					exit();
				}
		
			}
			else if(( strpos($url, '/id-post-image-redirect/') === 0 ) ) {
				$id = intval($_GET['id']);
				$post = get_post($id);
				
				$size = isset($_GET['size']) ? $_GET['size'] : 'thumbnail';
		
				if($post) {
			
					$thumbnail_id = get_post_thumbnail_id($id);
			
					$thumbnail_source = null;
					$thumbnail_size = null;
					if($thumbnail_id) {
						$thumbnail_data = wp_get_attachment_image_src($thumbnail_id, $size);
						$thumbnail_source = $thumbnail_data[0];
				
						wp_redirect($thumbnail_source);
		
						exit();
					}
					else {
						//METODO
						//exit();
					}
				}
			}
			else if(( strpos($url, '/id-image-redirect/') === 0 ) ) {
				$id = intval($_GET['id']);
				$post = get_post($id);
				
				$size = isset($_GET['size']) ? $_GET['size'] : 'thumbnail';
		
				if($post) {
			
					$thumbnail_data = wp_get_attachment_image_src($id, $size);
					$thumbnail_source = $thumbnail_data[0];
			
					wp_redirect($thumbnail_source);
	
					exit();
				}
			}
		}
		
		public static function test_import() {
			echo("Imported \Wprr\RedirectHooks<br />");
		}
	}
?>
