<?php
	namespace MRouterData;
	
	use \WP_Query;
	
	// \MRouterData\NoticeHooks
	class NoticeHooks {
		
		protected $settings = null;
		
		function __construct() {
			//echo("\MRouterData\NoticeHooks::__construct<br />");
			
			
		}
		
		public function register() {
			//echo("\MRouterData\NoticeHooks::register<br />");
			
			add_action('admin_notices', array($this, 'hook_admin_notices'));
			
		}
		
		
		
		public function hook_admin_notices() {
			//echo("\MRouterData\NoticeHooks::hook_admin_notices<br />");
			
			$screen = get_current_screen();
			//var_dump($screen);
		
			if(!$screen) {
				return;
			}
		
			global $post;
			
			//MEYODO: selection
		}
		
		protected function output_notice($module_name, $data, $type = '') {
			$element_id = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
			?>
			
				<div class="notice <?php echo($type); ?>" id="<?php echo($element_id); ?>">
					<script type="text/javascript">
						window.OA.reactModuleCreator.createModule("<?php echo($module_name); ?>", document.getElementById("<?php echo($element_id); ?>"), <?php echo(json_encode($data)); ?>);
					</script>
				</div>
			
			<?php
		}
		
		public static function test_import() {
			echo("Imported \MRouterData\NoticeHooks<br />");
		}
	}
?>