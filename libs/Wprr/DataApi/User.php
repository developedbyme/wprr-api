<?php
	namespace Wprr\DataApi;

	// \Wprr\DataApi\User
	class User {
		
		protected $_has_loaded = false;
		protected $_user_data = null;
		protected $_token = null;

		function __construct() {
			
		}
		
		public function start_session() {
			if(!$this->_has_loaded) {
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
						$this->_token = $token;
	
						if($expiration > time()) {
		
							$user_data = $db->query_first('SELECT ID as id, user_pass, user_email as email, display_name as name FROM wp_users WHERE user_login = "'.$db->escape($user_login).'" LIMIT 1');
				
							$user_data['id'] = (int)$user_data['id'];
							$user_data['login'] = $user_login;
							
							$pass = $user_data['user_pass'];
							unset($user_data['user_pass']);
							$pass_frag = substr($pass, 8, 4);
		
							$hash_key = $user_login . '|' . $pass_frag . '|' . $expiration . '|' . $token;
							$key = hash_hmac( 'md5', $hash_key, LOGGED_IN_KEY.LOGGED_IN_SALT );
		
							$hash = hash_hmac('sha256', $user_login . '|' . $expiration . '|' . $token, $key );
		
							if($hash === $hmac) {
								$hashed_token = hash( 'sha256', $token );
			
								$session_tokens = unserialize($db->query_first('SELECT meta_value FROM wp_usermeta WHERE user_id = '.$user_data['id'].' AND meta_key = "session_tokens" LIMIT 1')['meta_value']);
					
								if(isset($session_tokens[$hashed_token])) {
									$this->_user_data = $user_data;
								}
							}
						}
					}
				}
			
				$this->_has_loaded = true;
			}
		}
		
		public function is_signed_in() {
			$this->start_session();
			return $this->_user_data != null;
		}
		
		public function get_user_data() {
			$this->start_session();
			return $this->_user_data;
		}
		
		public function get_rest_nonce() {
			$rest_nonce = null;
			
			if($this->is_signed_in()) {
				$action = 'wp_rest';
				$i = ceil( time() / ( NONCE_LIFE / 2 ) );

				$rest_hash = hash_hmac('md5', $i . '|' . $action . '|' . $this->_user_data['id'] . '|' . $this->_token, NONCE_KEY.NONCE_SALT );
				$rest_nonce = substr($rest_hash, -12, 10 );
			}
			
			
			return $rest_nonce;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\User<br />");
		}
	}
?>