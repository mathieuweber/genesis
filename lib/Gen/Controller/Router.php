<?php
/**
 * @category   Gen
 * @package	Gen_Controller
 */
class Gen_Controller_Router
{
	
	const CONSTANT = 'Gen_Controller_Router_Route_Constant';
	
	const SEPARATOR = '/';
	
	const MARKER = ':';
	
	const DEFAULT_REGEXP = '([a-zA-Z_.-]+)';
	
	/**
	 * Application Base Url
	 * @var string
	 */
	protected static $_baseUrl = '/';
	
	/**
	 * Application Server Name
	 * @var string
	 */
	protected static $_serverName = '';
	
	/**
	 * Hash of name Routes
	 * @var Gen_Hash
	 */
	protected $_routes = array();
	
	/**
	 * Sets the base url
	 *
	 * @param  string $baseUrl
	 * @return void
	 */
	public static function setBaseUrl($baseUrl)
	{
		self::$_baseUrl = $baseUrl;
	}
	
	/**
	 * Gets the base url
	 *
	 * @return string
	 */
	public static function getBaseUrl()
	{
		return self::$_baseUrl;
	}
	
	/**
	 * Sets the server name
	 *
	 * @param string $serverName
	 * @return void
	 */
	public static function setServerName($serverName)
	{
		self::$_serverName = $serverName;
	}
	
	/**
	 * Gets the server name
	 *
	 * @return string
	 */
	public static function getServerName()
	{
		return self::$_serverName;
	}
	
	/** Cleans a given url
	 *
	 * removes the GET parameters
	 * removes the base url
	 *
	 * @example www.mysite.com/base/url/thing/to/do?option=foo
	 * is turned into `thing/to/do`
	 *
	 * @param  string url
	 * @return string $cleanUrl
	 */
	public static function cleanUrl($url)
	{
		$pattern = '#^' . self::$_baseUrl . '((.*[^/])/?)?$#';
		$parts = explode('?', $url);
		
		return preg_replace($pattern, '$1', $parts[0]);
	}
	
	public function getRoute($name)
	{
		return isset($this->_routes[$name]) ? $this->_routes[$name] : null;
	}
	
	public function getRoutes()
	{
		return $this->_routes;
	}
	
	public function setRoutes(array $routes)
	{
		$this->_routes = $routes;
		return $this;
	}
	
	public function route(Gen_Controller_Request $request)
	{
		$url = self::cleanUrl($request->getUrl());
		$url = rtrim($url,"/");
		
		Gen_Log::log('url to be matched : ' . $url, 'Gen_Controller_Router::route', 'info');
		
		foreach($this->_routes as $route) {
			if($params = self::match($route, $url)) {
				/* set request controller, action and params */
				if (!isset($params['controller']) || !isset($params['action'])) {
					Gen_Log::log('No controller or action defined', 'Gen_Controller_Router::route', 'warning');
					Gen_Log::log($route, 'Gen_Controller_Router::route', 'warning');
					Gen_Log::log($params, 'Gen_Controller_Router::route', 'warning');
					return false;
				}
				$request
					->setController($params['controller'])
					->setAction($params['action'])
					->addParams($params)
					->addParams($_GET)
					->addParams($_POST);
				
				if(isset($params['module'])) {
					$request->setModule($params['module']);
				}
				
				$format = isset($params['format']) ? $params['format'] : $request->getParam('format');
				$request->setFormat($format);
				
				return $request;
			}
		}
		Gen_Log::log('No match found :(', 'Gen_Controller_Router::route', 'warning');
		Gen_Log::log($this->_routes, 'Gen_Controller_Router::route', 'info');
		return false;   
	}
	
	public function url($name = 'default', array $data = array(), $relative = true)
	{
		if ($route = $this->getRoute($name))
		{
			$server = $relative ? '' : 'http://' . self::$_serverName;
			return $server . self::$_baseUrl . self::buildUrl($route, $data);
		} else {
			require_once('Gen/Controller/Exception.php');
			throw new Gen_Controller_Exception("Undefined Route: $name in Gen_Controller_Router");
		}
	}
	
	/**
	 * 
	 * @param $name name of the path, for exemple event_show 
	 * @param $pattern url with :var for each var, for example /event/:id/show
	 * @param $defaults default element, must have controller, action and optionally a format, a module or a variable's default value
	 * @param $req matches the var with a regexp
	 */
	public function addRoute($name, $pattern, $defaults = array(), $req = array())
	{
		$route['name'] = $name;
		$route['pattern'] = preg_replace('#\(|\)|\?#', '', $pattern);
		$route['defaults'] = $defaults;
		
		preg_match_all('#:([a-z0-9_]+)#', $pattern, $matches);
		$vars = isset($matches[1]) ? $matches[1] : null;
		$regexp = $pattern;
		foreach ($vars as $var) {
			$replace = isset($req[$var]) ? $req[$var] : self::DEFAULT_REGEXP;
			$replace = '(?<'.$var.'>'.$replace.')';
			$regexp = str_replace(':' . $var, $replace,$regexp);
		}
		$route['vars'] = $vars;
		$route['regexp'] = '#^'.$regexp.'$#';
		
		$this->_routes[$name] = $route;
		return $this;
	}
	
	public static function match(array $route, $url)
	{
		$result = $route['defaults'];
		if (preg_match_all($route['regexp'], $url, $matches)) {
			foreach($route['vars'] as $var) {
				if (isset($matches[$var])) {
					$result[$var] = $matches[$var][0];
				}
			}
			return $result;
		}
		return false;
	}
	
	/**
	 * create a url with a root and data
	 * @param array $route
	 * @param array $data
	 */
	public static function buildUrl(array $route, array $data)
	{
		$result = array();
		$url = $route['pattern'];
		foreach($route['vars'] as $var) {
			$replace = null;
			if(isset($data[$var])) {
				$replace = $data[$var];
				unset($data[$var]);
			} 
			$url = str_replace(':'.$var, $replace, $url);
		}
		$url = rtrim($url, '/');
		
		$anchor = null;
		if (isset($data['#'])) {
			$anchor = '#' . $data['#'];
			unset($data['#']);
		}
		
		foreach ($data as $key => $row) {
			if($row !== null && $row !== '') {
				$getMethod = 1;
			}
			else{
				unset($data[$key]);
			}
		}
		$query = isset($getMethod) ? '?' . http_build_query($data) : '';
		
		return $url . $query . $anchor;
	}
}