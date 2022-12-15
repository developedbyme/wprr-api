<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\WpmlLanguage
	class WpmlLanguage {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("WpmlLanguage::select");
			
			if(!isset($data['language'])) {
				throw(new \Exception('No language parameter'));
			}
		}
		
		public function filter($post_ids, $data) {
			//var_dump("WpmlLanguage::filter");
			
			global $wprr_data_api;
			$db = $wprr_data_api->database();
			
			$language = $data['language'];
			
			$query = 'SELECT element_id as id, element_type as type, language_code as language FROM '.DB_TABLE_PREFIX.'icl_translations WHERE element_id IN ('.implode(',', $post_ids).')';
			$results = $db->query($query);
			
			$language_map = array();
			foreach($results as $result) {
				if($result['type'][0] === 'p') {
					$language_map[$result['id']] = $result['language'];
				}
			}
			
			$selected_posts = array();
			foreach($post_ids as $post_id) {
				if($language_map[$post_id] === $language) {
					$selected_posts[] = $post_id;
				}
			}
			
			return $selected_posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\WpmlLanguage<br />");
		}
	}
?>