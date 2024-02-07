<?php
	namespace Wprr\Core\Admin\MetaData;
	
	// \Wprr\Core\Admin\MetaData\PostMetaDataBox
	class PostMetaDataBox {
		
		protected $_nonce_field_name = 'no-nonce';
		protected $_display_name = 'No name';
		protected $_meta_fields = array();
		
		function __construct() {
			//echo("\Core\Admin\MetaData\PostMetaDataBox::__construct<br />");
			
			
		}
		
		public function get_name() {
			return $this->_display_name;
		}
		
		public function set_name($display_name) {
			
			$this->_display_name = $display_name;
			
			return $this;
		}
		
		public function set_nonce_name($name) {
			
			$this->_nonce_field_name = $name;
			
			return $this;
		}
		
		public function create_simple_meta_fields($field_names) {
			foreach($field_names as $field_name) {
				$current_field = new \Wprr\Core\Admin\MetaData\MetaField();
				$current_field->set_name($field_name);
				$this->add_meta_field($current_field);
			}
		}
		
		public function add_meta_field($meta_field) {
			$this->_meta_fields[$meta_field->get_meta_key()] = $meta_field;
			
			return $this;
		}
		
		public function save($post_id) {
			//echo("\Core\Admin\MetaData\PostMetaDataBox::save<br />");
			
			foreach($this->_meta_fields as $meta_field) {
				$meta_field->save($post_id);
			}
			
		}
		
		public function verify_and_save($post_id) {
			//echo("\Core\Admin\MetaData\PostMetaDataBox::verify_and_save<br />");
			
			if (!isset($_POST[$this->_nonce_field_name]) || !wp_verify_nonce($_POST[$this->_nonce_field_name], basename(__FILE__).$post_id)) {
				return;
			}
			
			$this->save($post_id);
		}
		
		
		
		public function output_box_start($post) {
			?>
				<div id="recipe-metadata" class="postbox">
					<h3><span><?php _e($this->_display_name, WPRR_TEXTDOMAIN) ?></span></h3>
					<div class="inside">
			<?php
		}
		
		protected function output_content($post) {
			//echo("\Core\Admin\MetaData\PostMetaDataBox::output_content<br />");
			
			foreach($this->_meta_fields as $meta_field) {
				$meta_field->output($post);
			}
		}
		
		public function output_box_end($post) {
			?>
					</div>
				</div>
			<?php
		}
		
		public function output($post) {
			//echo("\Core\Admin\MetaData\PostMetaDataBox::output<br />");
			
			$this->output_box_start($post);
			$this->output_content($post);
			$this->output_box_end($post);
		}
		
		public function output_with_nonce($post) {
			//echo("\Core\Admin\MetaData\PostMetaDataBox::output_with_nonce<br />");
			
			wp_nonce_field(basename(__FILE__).$post->ID, $this->_nonce_field_name);
			$this->output($post);
		}
		
		public function output_registered_box() {
			//echo("\Core\Admin\MetaData\PostMetaDataBox::output_with_nonce<br />");
			
			global $post;
			
			wp_nonce_field(basename(__FILE__).$post->ID, $this->_nonce_field_name);
			$this->output_content($post);
		}
		
		public static function test_import() {
			echo("Imported \Core\Admin\MetaData\PostMetaDataBox<br />");
		}
	}
?>