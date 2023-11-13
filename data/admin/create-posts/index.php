<?php
	require_once("../../setup-endpoint.php");
	
	global $wprr_data_api;
	
	try {
		
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			$result = array();
			
			$user = $wprr_data_api->user()->get_user_for_call($_GET);
			$is_ok = $user->is_trusted();
			if(!$is_ok) {
				throw(new \Exception('User '.$as_user.' is not allowed to use admin function'));
			}
			
			$post_data = json_decode(file_get_contents('php://input'), true);
			$posts = $post_data['data'];
			
			$wordpress_editor = $wprr_data_api->wordpress()->editor();
			
			foreach($posts as $name => $data) {
				$ids = array();
				
				$amount = isset($data['amount']) ? $data['amount'] : 1;
				$post_type = isset($data['postType']) ? $data['postType'] : 'dbm_data';
				$types = $data['types'];
				//Check if array or string
				
				for($i = 0; $i < $amount; $i++) {
					$post_name = $name.' '.($i+1);
					$post = $wordpress_editor->create_post($post_type, $post_name);
					//$editor->add_term_by_path('dbm_type', 'post-type/'.$post_type);
					$ids[] = $post->get_id();
					$editor = $post->editor();
					
					foreach($types as $type) {
						$editor->add_term_by_path('dbm_type', $type);
					}
				}
				
				$result[$name] = $ids;
			}
			
			
			$wprr_data_api->output()->output_api_repsponse($result);
		}
		
		
	}
	catch(Exception $error) {
		$wprr_data_api->output()->output_error($error->getMessage());
	}
?>