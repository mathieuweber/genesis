<?php
require_once('Gen/Http/Security.php');

class Gen_Http_Cookie
{
	const DEFAULT_NAME = 'Gen_Cookie';
	const DEFAULT_TIMEOUT = 1728000; // 20days * 24h * 60min * 60s
	const SEPARATOR_DATA = '¤';
	const SEPARATOR_KEY = '|';
	
	public static $key = '4b6Ad-8f5gD-h2jFk-6l9mG-o5H4J-ty2UI';
	
	public static $baseUrl = '/';
	
	protected $_name;
	
	protected $_data;
	
	protected $_expire;
	
	protected $_domain;
	
	protected $_salt;
	
	public function __construct($name = self::DEFAULT_NAME, $path = '', $expire = null)
	{
		$this->_name = md5($name . self::$key);
		$this->_data = array();
		$this->_expire = isset($expire) ? (int) $expire : (time() + self::DEFAULT_TIMEOUT);
		$this->setPath($path);
		$this->_domain = $_SERVER['SERVER_NAME'];
		$this->_salt = Gen_Http_Security::generateSalt();
		$this->read();
	}
	
	public function setExpire($expire)
	{
		$this->_expire = (int) $expire;
		if(isset($this->_data)) {
			$this->write();
		}
	}
	
	public function setPath($path)
	{
		$path = trim(self::$baseUrl .$path, '/\\').'/';
		if ($path{0} != '/') $path = '/'.$path;
		$path = rawurlencode($path);
		$path = str_replace('%2F', '/', $path);
		$path = str_replace('%7E', '~', $path);
		$this->_path = $path;
	}
	
	public function setParam($key, $value)
	{
		if(preg_match('#¤|\|#', $key . $value)) {
			require_once('Gen/Http/Exception.php');
			throw new Gen_Http_Exception('Forbidden chars in cookie');
		}
		$this->_data[$key] = $value;
		$this->write();
	}
	
	public function getParam($key, $default = null)
	{
		return isset($this->_data[$key]) ? $this->_data[$key] : $default;
	}
	
	public function unsetParam($key)
	{
		$value = null;
		if(isset($this->_data[$key])) {
			$value = $this->_data[$key];
			unset($this->_data[$key]);
		}
		$this->write();
		return $value;
	}
	
	public function setArray($key, array $data)
	{
		$this->setParam($key, serialize($data));
		return $this;
	}
	
	public function getArray($key)
	{
		return unserialize($this->getParam($key));
	}
	
	public function write()
	{
		if (empty($this->_data)) {
			return $this->reset();
		}
		$content = null;
		
		$this->_data['salt'] = $this->_salt;
		unset($this->_data['token']);
		
		foreach($this->_data as $key => $value)
		{
			$content .= $key . self::SEPARATOR_KEY . $value . self::SEPARATOR_DATA;
		}
		$content .= 'token' . self::SEPARATOR_KEY . Gen_Http_Security::generateToken($content);
		
		if(!setcookie($this->_name, $content, $this->_expire, $this->_path, $this->_domain, 0, true)) {
			throw new Exception('Unable to set cookie for domain ' . $this->_domain);
		}
		return $this;
	}
	
	public function read()
	{
		$this->_data = array();
		if (isset($_COOKIE[$this->_name]))
		{
			$content = substr($_COOKIE[$this->_name], 0, strpos($_COOKIE[$this->_name], 'token'));
			
			$params = explode(self::SEPARATOR_DATA, $_COOKIE[$this->_name]);
			foreach($params as $param)
			{
				$parts = explode(self::SEPARATOR_KEY, $param);
				$this->_data[$parts[0]] = $parts[1];
			}
			if (isset($this->_data['token']) && Gen_Http_Security::validateToken($content, $this->_data['token'])) {
				return true;
			}
			$this->reset();
			return false;
		}
		return true;
	}
	
	public function reset()
	{
		unset($this->_data);
		$this->_data = array();
		setcookie($this->_name, false, time() - 3600, $this->_path, $this->_domain, 0, true);
		unset($_COOKIE[$this->_name]);
		return true;
	}
}