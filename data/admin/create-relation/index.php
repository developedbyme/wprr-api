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
			$from_id = $post_data['from'];
			$to_id = $post_data['to'];
			$type = $post_data['type'];
			
			$wordpress_editor = $wprr_data_api->wordpress()->editor();
			
			$post = $wordpress_editor->create_post('dbm_object_relation', $from_id.' '.$type.' '.$to_id);
			$editor = $post->editor();
			$editor->add_term_by_path('dbm_type', 'object-relation');
			$editor->add_term_by_path('dbm_type', 'object-relation/'.$type);
			
			$time = -1;
			if(!isset($data['skipStart']) || !$data['skipStart']) {
				$time = time();
			}
			
			$editor->add_meta('fromId', $from_id);
			$editor->add_meta('toId', $to_id);
			$editor->add_meta('startAt', $time);
			$editor->add_meta('endAt', -1);
			
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