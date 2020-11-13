<?php
	namespace Wprr\RestApi;
	
	use \WP_Query;
	use \Wprr\OddCore\RestApi\EndPoint as EndPoint;
	
	// \Wprr\RestApi\UsersEndpoint
	class UsersEndpoint extends EndPoint {
		
		function __construct() {
			//echo("\OddCore\RestApi\UsersEndpoint::__construct<br />");
			
			
		}
		
		protected function get_filters($names, $data) {
			$return_array = array();
			foreach($names as $name) {
				$has_permission = apply_filters(WPRR_DOMAIN.'/has_permission_for_users/'.$name, true, $data);
				if($has_permission) {
					$return_array[] = $name;
				}
				else {
					//METODO: log error
				}
			}
			
			return $return_array;
		}
		
		public function perform_call($data) {
			//echo("\OddCore\RestApi\UsersEndpoint::perform_call<br />");
			
			$selections = $this->get_filters(explode(',', $data['selections']), 'selection', $data);
			$encodings = $this->get_filters(explode(',', $data['encodings']), 'encoding', $data);
			
			$general_has_permission_filter_name = WPRR_DOMAIN.'/has_permission_for_users';
			
			$has_permission = apply_filters($general_has_permission_filter_name, current_user_can('list_users'), $data);
			
			if(!$has_permission) {
				return $this->output_error('Access denied');
			}
			
			do_action(WPRR_DOMAIN.'/prepare_api_request', $data);
			
			$query_args = array(
				'fields' => 'ids'
			);
			
			if(isset($data['order'])) {
				$query_args['order'] = $data['order'];
			}
			if(isset($data['orderby'])) {
				$query_args['orderby'] = $data['orderby'];
			}
			
			try {
				foreach($selections as $selection) {
					$filter_name = WPRR_DOMAIN.'/user_query/'.$selection;
				
					$query_args = apply_filters($filter_name, $query_args, $data);
				}
			
				$users = get_users($query_args);
				foreach($selections as $selection) {
					$filter_name = WPRR_DOMAIN.'/user_filter/'.$selection;
				
					$users = apply_filters($filter_name, $users, $data);
				}
			
				$encoded_users = array();
				foreach($users as $user_id) {
				
					$encoded_data = array('id' => $user_id);
				
					foreach($encodings as $encoding) {
						$filter_name = WPRR_DOMAIN.'/user_encoding/'.$encoding;
				
						$encoded_data = apply_filters($filter_name, $encoded_data, $user_id, $data);
					}
				
					$encoded_users[] = $encoded_data;
				};
			}
			catch(\Exception $exception) {
				return $this->output_error($exception->getMessage());
			}
			
			return $this->output_success($encoded_users);
		}
		
		public static function test_import() {
			echo("Imported \OddCore\RestApi\UsersEndpoint<br />");
		}
	}
?>