<?php
	namespace Wprr\DataApi\Data\Action\Actions;

	// \Wprr\DataApi\Data\Action\Actions\PrivateFile
	class PrivateFile {

		function __construct() {
			
		}
		
		public static function apply_action($return_value, $data) {
			//var_dump("PrivateFile::apply_action");
			
			global $wprr_data_api;
			
			$id = (int)$data['id'];
			
			if(!$id) {
				throw(new \Exception('Parameter id not specified'));
			}
			
			$post = $wprr_data_api->wordpress()->get_post($id);
			
			$file_path = $post->get_meta('path');
			if(!$file_path) {
				$file_path = implode(UPLOAD_DIR, explode(UPLOAD_URL, $post->get_meta('url')));
			}
			
			if(!$wprr_data_api->user()->is_signed_in()) {
				
				$sign_in_url = SITE_URL.'/'.'sign-in';
				
				$requested_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			
				$sign_in_url .= '?redirect_to='.urlencode($requested_url);
				
				return array('format' => 'redirect', 'data' => $sign_in_url);
			}
			$user = $wprr_data_api->user()->get_user_for_call($data);
			$is_ok = $user->is_trusted();
			
			if(!$is_ok) {
				$access_rule = $post->single_object_relation_query('in:for:type/access-rule');
				
				if(!$access_rule) {
					throw(new \Exception('File does not have access rule'));
				}
				
				$identifier = $access_rule->get_meta('identifier');
				
				$is_ok = $wprr_data_api->registry()->apply_filters('accessRules/'.$identifier.'/hasAccess', false, $post, $user, $access_rule);
			}
			
			if(!$is_ok) {
				throw(new \Exception('User '.$as_user.' is not allowed to get file'));
			}
			
			$handle = fopen($file_path, "r");
			$contents = fread($handle, filesize($file_path));
			fclose($handle);
			
			$return_data = array('format' => 'file', 'data' => $contents, 'meta' => array('contentType' => mime_content_type($path)));
			
			return $return_data;
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\PrivateFile<br />");
		}
	}
?>