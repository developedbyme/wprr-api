<?php
	namespace MRouterData\OddCore\Admin\Pages;
	
	use MRouterData\OddCore\Admin\Pages\Page as Page;
	
	// \MRouterData\OddCore\Admin\Pages\ReactPage
	class ReactPage extends Page {
		
		protected $_holder_id = null;
		protected $_component_name = null;
		protected $_data = null;
		
		function __construct() {
			//echo("\OddCore\Admin\Pages\ReactPage::__construct<br />");
			
			$this->_holder_id = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
		}
		
		public function set_component($name, $data = null) {
			$this->_component_name = $name;
			$this->_data = $data;
			
			return $this;
		}
		
		protected function get_react_data() {
			
			return $this->_data;
		}
		
		public function output() {
			//echo("\OddCore\Admin\Pages\ReactPage::output<br />");
			
			?>
				<div id="<?php echo($this->_holder_id); ?>"></div>
				<script>
					window.OA.reactModuleCreator.createModule("<?php echo($this->_component_name); ?>", document.getElementById("<?php echo($this->_holder_id); ?>"), <?php echo(json_encode($this->get_react_data())); ?>);
				</script>
			<?php
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\Pages\ReactPage<br />");
		}
	}
?>