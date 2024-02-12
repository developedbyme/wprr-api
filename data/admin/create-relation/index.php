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
			
			//METODO: validate from, to, type
			
			$from_post = $wprr_data_api->wordpress()->get_post($post_data['from']);
			$to_post = $wprr_data_api->wordpress()->get_post($post_data['to']);
			
			$wordpress_editor = $wprr_data_api->wordpress()->editor();
			
			$time = -1;
			if(!isset($data['skipStart']) || !$data['skipStart']) {
				$time = time();
			}
			
			$post = $wordpress_editor->create_relation($from_post, $to_post, $post_data['type'], $time);
			$editor = $post->editor();
			
			if(isset($data['makePrivate']) && $data['makePrivate']) {
				$editor->make_private('private');
			}
			
			$result['id'] = $post->get_id();
			
			$wprr_data_api->output()->output_api_repsponse($result);
		}
		
		
	}
	catch(Exception $error) {
		$wprr_data_api->output()->output_error($error->getMessage());
	}
?>