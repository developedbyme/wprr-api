<?php
	namespace Wprr\Core\Admin\MetaData;
	
	use \Wprr\Core\Admin\MetaData\PostMetaDataBox;
	
	// \Wprr\Core\Admin\MetaData\ReactPostMetaDataBox
	class ReactPostMetaDataBox extends PostMetaDataBox {
		
		protected $_holder_id = null;
		protected $_component_name = null;
		protected $_data = null;
		
		function __construct() {
			//echo("\Core\Admin\MetaData\ReactPostMetaDataBox::__construct<br />");
			
			$this->_holder_id = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
			
			//$portrait_image_meta_field = new \Wprr\Core\Admin\MetaData\MetaField();
			//$portrait_image_meta_field->set_name('portrait_image');
			//$this->add_meta_field($portrait_image_meta_field);
		}
		
		public function set_component($name, $data = null) {
			$this->_component_name = $name;
			$this->_data = $data;
			
			return $this;
		}
		
		protected function get_react_data($post) {
			
			$id = $post->ID;
			
			//$thumbnail_id = get_post_thumbnail_id($id);
			//$this->_data['initialImage'] = ($thumbnail_id) ? wp_get_attachment_image_src($thumbnail_id, array(1920, 1080))[0] : NULL;
			$this->_data['removeFeaturedImageNonce'] = wp_create_nonce('set_post_thumbnail-'.$id);
			
			//$portrait_image_id = $this->_meta_fields['portrait_image']->get_value($post);
			//$this->_data['initialPortraitImage'] = ($portrait_image_id) ? wp_get_attachment_image_src($portrait_image_id, array(768, 1200))[0] : NULL;
			
			$meta_fields_data = array();
			foreach($this->_meta_fields as $meta_field) {
				$meta_fields_data[$meta_field->get_field_name($post)] = $meta_field->get_value($post);
			}
			$this->_data['metaFields'] = $meta_fields_data;
			
			return $this->_data;
		}
		
		public function output_box_start($post) {
			?>
				<div id="recipe-metadata" class="postbox">
					<h3><span><?php _e($this->_display_name, WPRR_TEXTDOMAIN) ?></span></h3>
					<div class="inside" id="<?php echo($this->_holder_id); ?>">
			<?php
		}
		
		protected function output_content($post) {
			//echo("\Core\Admin\MetaData\ReactPostMetaDataBox::output_content<br />");
			
			?>
				<script>
					window.OA.reactModuleCreator.createModule("<?php echo($this->_component_name); ?>", document.getElementById("<?php echo($this->_holder_id); ?>"), <?php echo(json_encode($this->get_react_data($post))); ?>);
				</script>
			<?php
		}
		
		public static function test_import() {
			echo("Imported \Core\Admin\MetaData\ReactPostMetaDataBox<br />");
		}
	}
?>