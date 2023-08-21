<?php
	namespace Wprr\DataApi;

	// \Wprr\DataApi\HttpRequest
	class HttpRequest {

		function __construct() {
			
		}
		
		public function load($url, $headers = null) {
			
			$all_headers = array();
			
			if($headers) {
				foreach($headers as $name => $value) {
					$all_headers[] = $name . ': ' . $value;
				}
			}
			
			$ch = curl_init();
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_URL, $url);
			//curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $all_headers);
			$data = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			/*
			echo($url);
			echo("\n");
			echo($httpcode);
			echo("\n");
			echo($data);
			echo("\n\n");
			*/
			
			return array('url' => $url, 'code' => $httpcode, 'data' => $data);
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\HttpRequest<br />");
		}
	}
?>