<?php

class Gen_Http_Security
{
	const DATA_SEPARATOR = '¤';
	
	const KEY_SEPARATOR = '|';
	
	public static $seed = 'AFg65-Tio89-3vbjU-I3n9d-D4577-GH56i';
	
	public static $salt = '1y5E6-k8F8l-79mG6-Hss56-JfHg3-2gBNu';
	
	const DEFAULT_TIMEOUT = 600; // 10 min * 60s
	
	public static function generateSalt()
	{
		return md5(self::$salt . microtime() . rand());
	}
	
	public static function generateToken($params)
	{
		if (is_array($params)) {
			ksort($params);
			$str = null;
			foreach ($params as $key => $value) {
				$str .= $key . self::KEY_SEPARATOR . $value . self::DATA_SEPARATOR;
			}
			$params = rtrim($str, self::DATA_SEPARATOR);
		}
		$token = md5(self::$seed . $params);
		return $token;
	}
	
	public static function validateToken($params, $challenge) {
		$token = self::generateToken($params);
		if ($token === $challenge) {
			return true;
		}
		return false;
	}
	
	public static function sign($params)
	{
		$params = (array) $params;
		$params['salt'] = self::generateSalt();
		$params['exectime'] = time();
		$params['token'] = self::generateToken($params);
		return $params;
	}
	
	public static function verify(array $params, $timeout = self::DEFAULT_TIMEOUT)
	{
		return self::verifyToken($params) && self::verifyTimeout($params, $timeout);
	}
	
	public static function verifyToken(array $params)
	{
		if(!isset($params['token'])) {
			return false;
		}
		
		$token = $params['token'];
		unset($params['token']);
		return self::validateToken($params, $token);
	}
	
	public static function verifyTimeout(array $params, $timeout = self::DEFAULT_TIMEOUT)
	{
		if (isset($params['exectime']) && (time() - $params['exectime'] > $timeout)) {
			return false;
		}
		
		return true;
	}
}