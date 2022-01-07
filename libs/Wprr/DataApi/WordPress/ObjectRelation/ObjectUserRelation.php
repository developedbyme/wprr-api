<?php
	namespace Wprr\DataApi\WordPress\ObjectRelation;

	// \Wprr\DataApi\WordPress\ObjectRelation\ObjectUserRelation
	class ObjectUserRelation {
		
		protected $_type = null;
		protected $_post = null;
		
		function __construct() {
			
		}
		
		public function setup($type, $post) {
			$this->_type = $type;
			$this->_post = $post;
			
			return $this;
		}
		
		public function get_id() {
			return $this->_post->get_id();
		}
		
		public function has_object_type($term) {
			$object = $this->get_object();
			return $object->has_term($term);
		}
		
		public function is_active_at($time) {
			
			$start_time = (int)$this->_post->get_meta('startAt');
			if($time < $start_time && $start_time !== -1) {
				return false;
			}
			
			$end_time = (int)$this->_post->get_meta('endAt');
			if($time > $end_time && $end_time !== -1) {
				return false;
			}
			
			return true;
		}
		
		public function get_object_id() {
			
			$id = (int)$this->_post->get_meta('fromId');
			
			return $id;
		}
		
		public function get_user_id() {
			
			$id = (int)$this->_post->get_meta('toId');
			
			return $id;
		}
		
		public function get_object() {
			global $wprr_data_api;
			$wp = $wprr_data_api->wordpress();
			
			return $wp->get_post($this->get_object_id());
		}
		
		public function get_user() {
			global $wprr_data_api;
			$wp = $wprr_data_api->wordpress();
			
			return $wp->get_user($this->get_user_id());
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\ObjectRelation\ObjectUserRelation<br />");
		}
	}
?>