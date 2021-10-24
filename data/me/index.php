<?php
	
	error_reporting(E_ALL);
	
	require_once("../../setup.php");
	require_once("../settings.php");
	
	global $wprr_data_api;
	$db = $wprr_data_api->database();
	
	$cookie_hash = 'wordpress_logged_in_' . md5( SITE_URL );
	$cookie = $_COOKIE[ $cookie_hash ];
	
	if($cookie) {
		$cookie_parts = explode( '|', $cookie );
		
		if(count($cookie_parts) >= 4) {
			$user_login = $cookie_parts[0];
			$expiration = 1*$cookie_parts[1];
			$token = $cookie_parts[2];
			$hmac = $cookie_parts[3];
	
			if($expiration > time()) {
		
				$user_data = $db->query_first('SELECT ID, user_pass, user_email, display_name FROM wp_users WHERE user_login = "'.$db->escape($user_login).'" LIMIT 1');
				
				$user_data['id'] = (int)$user_data['ID'];
				unset($user_data['ID']);
				$user_data['user_login'] = $user_login;
		
				$pass_frag = substr( $user_data['user_pass'], 8, 4 );
		
				$hash_key = $user_login . '|' . $pass_frag . '|' . $expiration . '|' . $token;
				$key = hash_hmac( 'md5', $hash_key, LOGGED_IN_KEY.LOGGED_IN_SALT );
		
				$hash = hash_hmac('sha256', $user_login . '|' . $expiration . '|' . $token, $key );
		
				if($hash === $hmac) {
					$hashed_token = hash( 'sha256', $token );
			
					$session_tokens = unserialize($db->query_first('SELECT meta_value FROM wp_usermeta WHERE user_id = '.$user_data['id'].' AND meta_key = "session_tokens" LIMIT 1')['meta_value']);
					
					if(isset($session_tokens[$hashed_token])) {
				
						$action = 'wp_rest';
						$i = ceil( time() / ( NONCE_LIFE / 2 ) );
				
						$rest_hash = hash_hmac('md5', $i . '|' . $action . '|' . $user_data['id'] . '|' . $token, NONCE_KEY.NONCE_SALT );
						$rest_nonce = substr($rest_hash, -12, 10 );
				
						unset($user_data['user_pass']);
						$reposonse = array(
							'data' => array(
								'user' => $user_data,
								'restNonce' => $rest_nonce
							)
						);
				
						header('Content-Type: application/json; charset=utf-8');
						header('Cache-Control: no-cache, no-store, must-revalidate');
						header('Pragma: no-cache');
						header('Expires: 0');
						
						echo(json_encode($reposonse));
						die();
					}
				}
			}
		}
	}
	
	
?>