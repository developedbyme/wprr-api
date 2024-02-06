<?php
	namespace Wprr\DataApi;

	// \Wprr\DataApi\Files
	class Files {

		function __construct() {
			
		}
		
		public function make_sure_directory_exists($path) {
			return mkdir($path, 0777, true);
		}
		
		public function get_current_upload_dir() {
			
			$date_folder = date('Y/m', time());
			
			return UPLOAD_DIR.'/'.$date_folder;
		}
		
		public function copy($from_path, $to_path) {
			
			$direcory = dirname($to_path);
			$this->make_sure_directory_exists($direcory);
			
			return copy($from_path, $to_path);
		}
		
		public function get_unique_file_name($folder, $file_name) {
			if(file_exists($folder.'/'.$file_name)) {
				$file_names = scandir($folder, SCANDIR_SORT_ASCENDING);
				
				$file_name_parts = explode('.', $file_name);
				$extension = array_pop($file_name_parts);
				$name = implode('.', $file_name_parts);
				
				$i = 2;
				while(true) {
					$new_name = $name.'-'.$i.'.'.$extension;
					if(!in_array($new_name, $file_names)) {
						return $folder.'/'.$new_name;
					}
					$i++;
				}
			}
			
			return $folder.'/'.$file_name;
		}
		
		public function copy_to_uploads($from_path, $file_name = null) {
			
			if(!$file_name) {
				$file_name = basename($from_path);
			}
			
			$path = $this->get_current_upload_dir();
			
			$new_path = $this->get_unique_file_name($path, $file_name);
			
			$copied = $this->copy($from_path, $new_path);
			
			if($copied) {
				return $new_path;
			}
			
			return null;
		}
		
		public function get_absolute_path_in_uploads($path) {
			return UPLOAD_DIR.'/'.$path;
		}
		
		public function get_relative_path_in_uploads($path) {
			if(0 === strpos($path, UPLOAD_DIR)) {
				return substr($path, strlen(UPLOAD_DIR)+1);
			}
			
			return null;
		}
		
		public function get_relative_url_in_uploads($path) {
			if(0 === strpos($path, UPLOAD_URL)) {
				return substr($path, strlen(UPLOAD_URL)+1);
			}
			
			return null;
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\Files<br />");
		}
	}
?>