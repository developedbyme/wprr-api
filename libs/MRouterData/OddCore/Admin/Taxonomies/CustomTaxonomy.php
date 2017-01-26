<?php
	namespace MRouterData\OddCore\Admin\Taxonomies;
	
	// \MRouterData\OddCore\Admin\Taxonomies\CustomTaxonomy
	class CustomTaxonomy {
		
		protected $_system_name = null;
		
		protected $_labels = array(
			'name' => 'Custom taxonomys',
			'singular_name' => 'Custom taxonomy',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Custom taxonomy',
			'edit_item' => 'Edit Custom taxonomy',
			'new_item' => 'New Custom taxonomy',
			'all_items' => 'All Custom taxonomys',
			'view_item' => 'View Custom taxonomy',
			'search_items' => 'Search Custom taxonomys',
			'not_found' => 'No custom post types found',
			'not_found_in_trash' => 'No custom post types found in trash',
			'parent_item_colon' => '',
			'menu_name' => 'Custom taxonomys'
		);
		protected $_arguments = array();
		
		function __construct() {
			//echo("\OddCore\Admin\Taxonomies\CustomTaxonomy::__construct<br />");
			
			$this->_arguments = array(
				'hierarchical' => true,
				'public' => true,
				'show_ui' => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'show_tagcloud' => false,
				'rewrite' => array( 'slug' => $this->_system_name ),
			);
		}
		
		public function set_argument($name, $value) {
			$this->_arguments[$name] = $value;
			
			return $this;
		}
		
		public function get_system_name() {
			return $this->_system_name;
		}
		
		public function set_names($system_name, $dipslay_name = null) {
			//echo("\OddCore\Admin\Taxonomies\CustomTaxonomy::set_names<br />");
			
			$this->_system_name = $system_name;
			if($this->_arguments) {
				$this->_arguments['rewrite'] = array( 'slug' => $this->_system_name );
			}
			
			if(isset($dipslay_name)) {
				$this->setup_labels_autonaming($dipslay_name);
			}
			
			return $this;
		}
		
		public function setup_labels_autonaming($name) {
			
			if(substr($name, -1) === 'y') {
				$multiple_name = substr($name, 0, -1).'ies';
			}
			else {
				$multiple_name = $name.'s';
			}
			//METODO: fix parent_item_colon
			
			$this->_labels = array(
				'name' => __( ucfirst($multiple_name), M_ROUTER_DATA_TEXTDOMAIN ),
				'singular_name' => __( ucfirst($name), M_ROUTER_DATA_TEXTDOMAIN ),
				'add_new' => __( 'Add New', M_ROUTER_DATA_TEXTDOMAIN ),
				'add_new_item' => __( 'Add New '.ucfirst($name), M_ROUTER_DATA_TEXTDOMAIN ),
				'edit_item' => __( 'Edit '.ucfirst($name), M_ROUTER_DATA_TEXTDOMAIN ),
				'new_item' => __( 'New '.ucfirst($name), M_ROUTER_DATA_TEXTDOMAIN ),
				'all_items' => __( 'All '.ucfirst($multiple_name), M_ROUTER_DATA_TEXTDOMAIN ),
				'view_item' => __( 'View '.ucfirst($name), M_ROUTER_DATA_TEXTDOMAIN ),
				'search_items' => __( 'Search '.ucfirst($multiple_name), M_ROUTER_DATA_TEXTDOMAIN ),
				'not_found' => __( 'No '.$name.' found', M_ROUTER_DATA_TEXTDOMAIN ),
				'not_found_in_trash' => __( 'No '.$name.' found in trash', M_ROUTER_DATA_TEXTDOMAIN ),
				'parent_item_colon' => '',
				'menu_name' => __( ucfirst($multiple_name), M_ROUTER_DATA_TEXTDOMAIN )
			);
		}
		
		public function register() {
			//echo("\OddCore\Admin\Taxonomies\CustomTaxonomy::register<br />");
			
			$this->_arguments['labels'] = $this->_labels;
			
			register_taxonomy($this->_system_name, NULL, $this->_arguments);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\Admin\Taxonomies\CustomTaxonomy<br />");
		}
	}
?>