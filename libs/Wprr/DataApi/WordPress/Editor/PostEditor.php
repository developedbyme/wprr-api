<?php
	namespace Wprr\DataApi\WordPress\Editor;

	// \Wprr\DataApi\WordPress\Post\Editor
	class PostEditor {
		
		protected $_id = 0;
		
		function __construct() {
			
		}
		
		public function setup($id) {
			$this->_id = $id;
			
			return $this;
		}
		
		public function get_id() {
			return $this->_id;
		}
		
		public function post() {
			global $wprr_data_api;
			return $wprr_data_api->wordpress()->get_post($this->get_id());
		}
		
		public function add_term_by_id($term_id) {
			global $wprr_data_api;
			
			$wprr_data_api->performance()->start_meassure('PostEditor::add_term_by_id');
			
			$db = $wprr_data_api->database();
			
			$fields = array(
				'object_id' => $this->get_id(),
				'term_taxonomy_id' => $term_id,
			);
			
			$insert_statement = $this->get_insert_statement($fields);
			
			$query = 'INSERT INTO '.DB_TABLE_PREFIX.'term_relationships '.$insert_statement;
			
			$id = $db->insert($query);
			
			$wprr_data_api->performance()->stop_meassure('PostEditor::add_term_by_id');
			
			//METODO: invalidate data on post
			
			return $this;
		}
		
		public function add_term($term) {
			return $this->add_term_by_id($term->get_id());
		}
		
		public function add_term_by_path($taxonomy, $path) {
			global $wprr_data_api;
			
			$term = $wprr_data_api->wordpress()->get_taxonomy($taxonomy)->get_term($path);
			
			$this->add_term($term);
				
			return $this;
		}
		
		public function add_meta($key, $value) {
			global $wprr_data_api;
			
			$wprr_data_api->performance()->start_meassure('PostEditor::add_meta');
			
			$db = $wprr_data_api->database();
			
			if ( is_array( $value ) || is_object( $value ) ) {
				$value = serialize( $value );
			}
			
			$fields = array(
				'post_id' => $this->get_id(),
				'meta_key' => $key,
				'meta_value' => $value,
			);
			
			$insert_statement = $this->get_insert_statement($fields);
			
			$query = 'INSERT INTO '.DB_TABLE_PREFIX.'postmeta '.$insert_statement;
			
			$id = $db->insert($query);
			
			$wprr_data_api->performance()->stop_meassure('PostEditor::add_meta');
			
			$this->post()->invalidate_meta();
			
			return $this;
		}
		
		public function update_meta($key, $value) {
			
			global $wprr_data_api;
			$db = $wprr_data_api->database();
			
			$query = 'SELECT meta_id as id FROM '.DB_TABLE_PREFIX.'postmeta WHERE post_id = \''.$db->escape($this->get_id()).'\' AND meta_key = \''.$db->escape($key).'\'';
			
			$results = $db->query_without_storage($query);
			
			$ids = array_map(function($item) {return (int)$item['id'];}, $results);
			
			if(!empty($ids)) {
				$first_id = array_shift($ids);
				
				if ( is_array( $value ) || is_object( $value ) ) {
					$value = serialize( $value );
				}
				
				$query = 'UPDATE '.DB_TABLE_PREFIX.'postmeta SET meta_value = \''.$db->escape($value).'\' WHERE meta_id = '.$first_id;
			
				$result = $db->update($query);
				
				if(!empty($ids)) {
					$query = 'DELETE FROM '.DB_TABLE_PREFIX.'postmeta WHERE meta_id IN ('.implode(',', $ids).')';
			
					$result = $db->update($query);
				}
				$this->post()->invalidate_meta();
			}
			else {
				$this->add_meta($key, $value);
			}
			
			return $this;
		}
		
		public function delete_meta($key) {
			
			global $wprr_data_api;
			$db = $wprr_data_api->database();
			
			$query = 'DELETE FROM '.DB_TABLE_PREFIX.'postmeta WHERE post_id = \''.$db->escape($this->get_id()).'\' AND meta_key = \''.$db->escape($key).'\'';
			
			$result = $db->update($query);
			
			$this->post()->invalidate_meta();
			
			return $this;
		}
		
		//METODO: add generic update field
		
		public function change_status($status) {
			global $wprr_data_api;
			$db = $wprr_data_api->database();
			
			$query = 'UPDATE '.DB_TABLE_PREFIX.'posts SET post_status = \''.$db->escape($status).'\' WHERE id = '.$this->get_id();
			
			$result = $db->update($query);
			
			//METODO: invalidate post
			
			return $this;
		}
		
		public function make_private() {
			return $this->change_status('private');
		}
		
		public function add_outgoing_relation_by_name($post, $name, $start_time = -1, $make_private = true) {
			global $wprr_data_api;
			
			$term = $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term('object-relation/'.$name);
			
			$relation = $wprr_data_api->wordpress()->editor()->create_relation($this, $post, $term, $start_time);
			
			if($make_private) {
				$relation->editor()->change_status('private');
				
				$this->delete_meta('dbm/objectRelations/outgoing');
				$post->editor()->delete_meta('dbm/objectRelations/incoming');
				
				//METODO: invalidate post
			}
			
			return $relation;
		}
		
		public function add_incoming_relation_by_name($post, $name, $start_time = -1, $make_private = true) {
			global $wprr_data_api;
			
			$term = $wprr_data_api->wordpress()->get_taxonomy('dbm_type')->get_term('object-relation/'.$name);
			
			$relation = $wprr_data_api->wordpress()->editor()->create_relation($post, $this, $term, $start_time);
			
			if($make_private) {
				$relation->editor()->change_status('private');
				
				$post->editor()->delete_meta('dbm/objectRelations/outgoing');
				$this->delete_meta('dbm/objectRelations/incoming');
				
				//METODO: invalidate post
			}
			
			return $relation;
		}
		
		public function end_all_incoming_relations_by_name($name, $object_type = '*', $time = false) {
			$post = $this->post();
			
			$type = $post->get_incoming_direction()->get_type($name);
			
			if($time === false) {
				$time = time();
			}
			
			$relations = $type->get_relations($object_type, false);
			foreach($relations as $relation) {
				$end_time = $relation->end_at;
				if($end_time === -1 || $end_time >= $time) {
					$relation->post()->editor()->update_meta('endAt', $time);
					$relation->get_object()->editor()->delete_meta('dbm/objectRelations/outgoing');
					//METODO: invalidate post
				}
			}
			
			$this->delete_meta('dbm/objectRelations/incoming');
			//METODO: invalidate post
		}
		
		public function end_all_outgoing_relations_by_name($name, $object_type = '*', $time = false) {
			$post = $this->post();
			
			$type = $post->get_outgoing_direction()->get_type($name);
			
			if($time === false) {
				$time = time();
			}
			
			$relations = $type->get_relations($object_type, false);
			foreach($relations as $relation) {
				$end_time = $relation->end_at;
				if($end_time === -1 || $end_time >= $time) {
					$relation->post()->editor()->update_meta('endAt', $time);
					$relation->get_object()->editor()->delete_meta('dbm/objectRelations/incoming');
					//METODO: invalidate post
				}
			}
			
			$this->delete_meta('dbm/objectRelations/outgoing');
			//METODO: invalidate post
		}
		
		public function set_linked_property($identifier, $linked_post) {
			$object_property = $this->post()->single_object_relation_query_with_meta_filter('in:for:object-property', 'identifier', $identifier);
			if(!$object_property) {
				$object_property = wprr_get_data_api()->wordpress()->editor()->create_post('dbm_data', 'Object property '.$identifier.' for '.$this->post()->get_id());
				$object_property_editor = $object_property->editor();
				
				$object_property_editor->add_term_by_path('dbm_type', 'object-property');
				$object_property_editor->add_term_by_path('dbm_type', 'object-property/linked-object-property');
				$object_property_editor->add_term_by_path('dbm_type', 'identifiable-item');
				
				$object_property_editor->add_meta('identifier', $identifier);
				$object_property_editor->change_status('private');
				
				$this->add_incoming_relation_by_name($object_property, 'for');
				
			}
			else {
				$object_property->editor()->end_all_outgoing_relations_by_name('pointing-to');
			}
			
			$relation = $object_property->editor()->add_outgoing_relation_by_name($linked_post, 'pointing-to', time());
			
			return array('relation' => $relation, 'objectProperty' => $object_property);
		}
		
		public function set_order($new_order, $for_type = 'order') {
			
			$order = $this->post()->single_object_relation_query_with_meta_filter('out:relation-order-by:relation-order', 'forType', $for_type);
			if(!$order) {
				$order = wprr_get_data_api()->wordpress()->editor()->create_post('dbm_data', 'Order '.$for_type.' for '.$this->post()->get_id());
				$order_editor = $order->editor();
				
				$order_editor->add_term_by_path('dbm_type', 'relation-order');
				$order_editor->make_private();
				
				$this->add_outgoing_relation_by_name($order, 'relation-order-by');
			}
			
			$order->editor()->update_meta('order', $new_order);
			
			return $order;
		}
		
		public function get_insert_statement($fields) {
			
			global $wprr_data_api;
			
			$db = $wprr_data_api->database();
			
			$keys= array();
			$values = array();
			
			foreach($fields as $key => $value) {
				$keys[] = $key;
				
				if($value !== null) {
					$values[] = "'".$db->escape($value)."'";
				}
				else {
					$values[] = "null";
				}
			}
			
			return '('.implode(",", $keys).') VALUES ('.implode(",", $values).')';
		}
		
		public static function test_import() {
			echo("Imported \Wprr\DataApi\PostEditor<br />");
		}
	}
?>