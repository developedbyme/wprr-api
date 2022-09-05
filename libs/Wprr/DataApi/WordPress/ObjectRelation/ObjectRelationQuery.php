<?php
	namespace Wprr\DataApi\WordPress\ObjectRelation;

	// \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationQuery
	class ObjectRelationQuery {
		
		public static function get_posts($posts, $path) {
			
			global $wprr_data_api;
			$wp = $wprr_data_api->wordpress();
			
			$current_items = $posts;
			$path_parts = explode(',', $path);
			foreach($path_parts as $path_part) {
				$new_ids = array();
				$path_part_data = explode(':', $path_part);
				
				$direction_type = $path_part_data[0];
				$type = $path_part_data[1];
				
				if($direction_type === "user") {
					$time = isset($path_part_data[2]) ? (($path_part_data[2] === "*") ? false : 1*$path_part_data[2]) : time();
					
					$current_items = $wp->get_users($new_ids);
					foreach($current_items as $current_item) {
						$direction = $current_item->get_user_relations();
						$current_ids = $direction->get_type($type)->get_user_ids();
						var_dump($type, $direction->get_type($type));
						
						$new_ids = array_merge($new_ids, $current_ids);
					}
					
					//METODO: go on from user
					return $current_items;
				}
				else {
					$object_type = $path_part_data[2];
					$time = isset($path_part_data[3]) ? (($path_part_data[3] === "*") ? false : 1*$path_part_data[3]) : time();
					
					foreach($current_items as $current_item) {
						$direction = ($direction_type === "in") ? $current_item->get_incoming_direction() : $current_item->get_outgoing_direction();
						$current_ids = $direction->get_type($type)->get_object_ids($object_type, $time);
					
						$new_ids = array_merge($new_ids, $current_ids);
					
					}
					$current_items = $wp->get_posts($new_ids);
				}
			}
			
			return $current_items;
		}
		
		public static function get_single_post($post, $path) {
			$posts = self::get_posts(array($post), $path);
			
			if(empty($posts)) {
				return null;
			}
			
			return $posts[0];
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\ObjectRelation<br />");
		}
	}
?>