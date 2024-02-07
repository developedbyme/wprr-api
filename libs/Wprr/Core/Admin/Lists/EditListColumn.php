<?php
	namespace Wprr\Core\Admin\Lists;
	
	// \Wprr\Core\Admin\Lists\EditListColumn
	class EditListColumn {
		
		public $name = 'Master ingredient';
		
		function __construct() {
			//echo("\Core\Admin\Lists\EditListColumn::__construct<br />");
			
			
		}
		
		public function output($out, $term_id) {
			//echo("\Core\Admin\Lists\EditListColumn::output<br />");
			
			return "METODO";
		}
		
		public static function test_import() {
			echo("Imported \Core\Admin\Lists\EditListColumn<br />");
		}
	}
?>