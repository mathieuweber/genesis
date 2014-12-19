<?php
/**
 * @category   Gen
 * @package	Gen_Http
 */
class Gen_Http_Request
{
	/**
	 * Http Version
	 */
	const VERSION = 'HTTP/1.1';

	const USER_AGENT = 'Gen_Http_Request';

	const CONNECT_TIME_OUT = 120;

	const TIME_OUT = 120;
	
	/**
	 * Make an HTTP request
	 *
	 * @return an array["http_info", "http_code", "reponse"]
	 */
	public static function send($url, $method, $params = array(), $verifySSL = false, $httpHeader=array()) {
		$http_info = array();
		$httpHeader = array_merge($httpHeader, array('Expect:'));
		
		$ci = curl_init();
		/* Curl settings */
		curl_setopt($ci, CURLOPT_USERAGENT, self::USER_AGENT);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIME_OUT);
		curl_setopt($ci, CURLOPT_TIMEOUT, self::TIME_OUT);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ci, CURLOPT_HTTPHEADER, $httpHeader);
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $verifySSL);
		curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, $verifySSL ? 2 : 0);
		//curl_setopt($ci, CURLOPT_HEADERFUNCTION, "Gen_Http_Request::getHeader");
		curl_setopt($ci, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ci, CURLINFO_HEADER_OUT , true);

		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, true);
				if (is_array($params) && count($params)) $params = http_build_query($params);
				if ($params) curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
				break;
				
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				$url = self::buildUrl($url, $params);
				break;
			
			case 'GET':
			default:
				$url = self::buildUrl($url, (array) $params);
				break;
		}

		curl_setopt($ci, CURLOPT_URL, $url);
		$response = curl_exec($ci);
		$http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		$http_info = array_merge($http_info, curl_getinfo($ci));
		curl_close ($ci);
		
		return array (
			'info' => $http_info,
			'status' => $http_code,
			'response' => $response
		);
	}

	/**
	 * Get the header info to store.
	 */
	public static function getHeader($ch, $header) {
		$i = strpos($header, ':');
		if (!empty($i)) {
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
		}
		return strlen($header);
	}
	
	/**
	 * 
	 * @param string $url
	 * @param array $params
	 */
	public static function buildUrl($url, array $params = array(), $anchor = null){
		if (count($params)) {
			$url .= '?' . http_build_query($params);
		}
		if ($anchor){
			$url .= '#' . $anchor;
		}
		return $url;
	}
	
	public static function parseUrl($url)
	{
		$html = false;
		$stream = '';
		
		$handle = fopen($url, "rb");
		if (false !== $handle) {
			$stream = stream_get_contents($handle);
			$html=$stream;
			//$html = @simplexml_load_string($stream);
			fclose($handle);
		}
		return $html;
	}
}
