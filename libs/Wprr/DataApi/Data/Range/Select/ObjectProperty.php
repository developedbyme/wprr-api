<?php
	namespace Wprr\DataApi\Data\Range\Select;

	// \Wprr\DataApi\Data\Range\Select\ObjectProperty
	class ObjectProperty {

		function __construct() {
			
		}
		
		public function select($query, $data) {
			//var_dump("ObjectProperty::select");
			
			global $wprr_data_api;
			
			if(!isset($data['fromIds'])) {
				throw(new \Exception('fromIds not set'));
			}
			
			if(!isset($data['identifier'])) {
				throw(new \Exception('identifier not set'));
			}
			
			$from_ids = array_map(function($id) {return (int)$id;}, explode(',', $data['fromIds']));
			$object_property_posts = \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationQuery::get_posts($wprr_data_api->wordpress()->get_posts($from_ids), 'in:for:object-property');
			$from_posts = array();
			foreach($object_property_posts as $object_property_post) {
				if($object_property_post->get_meta('identifier') === $data['identifier']) {
					$from_posts[] = $object_property_post;
				}
			}
			
			
			if(!isset($data['path'])) {
				$posts = $from_posts;
			}
			else {
				$posts = \Wprr\DataApi\WordPress\ObjectRelation\ObjectRelationQuery::get_posts($from_posts, $data['path']);
			}
			
			
			$ids = array_map(function($post) {return $post->get_id();}, $posts);
			
			$query->include_only($ids);
		}
		
		public function filter($posts, $data) {
			//var_dump("ObjectProperty::filter");
			
			return $posts;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\ObjectProperty<br />");
		}
	}
?>