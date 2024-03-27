<?php
	namespace Wprr\DataApi;

	// \Wprr\DataApi\HttpRequest
	class HttpRequest {
		
		function __construct() {
			
		}
		
		public static function parse_headers($headers_string) {
			$headers = array();
			
			$headers_string = substr($headers_string, 0, strpos($headers_string, "\r\n\r\n"));
			
			foreach (explode("\r\n", $headers_string) as $i => $line) {
				if ($i > 0) {
					list ($key, $value) = explode(': ', $line);
					$headers[$key] = $value;
				}
			}
			
			return $headers;
		}
		
		
		public static  function load($url, $headers = null) {
			
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
			
			return array('url' => $url, 'code' => $httpcode, 'data' => $data);
		}
		
		public static  function put($url, $headers = null) {
			
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
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($ch, CURLOPT_HTTPHEADER, $all_headers);
			$data = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			return array('url' => $url, 'code' => $httpcode, 'data' => $data);
		}
		
		public static function load_including_headers($url, $headers = null) {
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
			curl_setopt($ch, CURLOPT_HEADER, true);
			
			$data = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			curl_close($ch);
			
			$body = substr($data, $headerSize);
			$header = substr($data, 0, $headerSize);
			$headers = self::parse_headers($header);
			
			return array('url' => $url, 'code' => $httpcode, 'data' => $body, 'headers' => $headers);
		}
		
		public static function post($url, $data, $headers) {
			
			$all_headers = array();
			
			if($headers) {
				foreach($headers as $name => $value) {
					$all_headers[] = $name . ': ' . $value;
				}
			}
			
			
			$fields_string = http_build_query($data);
			//echo($fields_string."<br />");
			
			$ch = curl_init();
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			//curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $all_headers);
			$data = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			return array('url' => $url, 'code' => $httpcode, 'data' => $data);
		}
		
		public static function post_json($url, $data, $headers, $method = null) {
			
			$all_headers = array();
			
			if($headers) {
				foreach($headers as $name => $value) {
					$all_headers[] = $name . ': ' . $value;
				}
			}
			
			$fields_string = json_encode($data);
			
			$ch = curl_init();
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_URL, $url);
			if($method) {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
			}
			else {
				curl_setopt($ch, CURLOPT_POST, TRUE);
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			//curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $all_headers);
			
			$data = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			return array('url' => $url, 'code' => $httpcode, 'data' => $data);
		}

		public static function test_import() {
			echo("Imported \Wprr\DataApi\HttpRequest<br />");
		}
	}
?>