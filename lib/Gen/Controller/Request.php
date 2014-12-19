<?php
/**
 * @category   Gen
 * @package	Gen_Controller
 */
class Gen_Controller_Request
{ 
	/**
	 * The Action 
	 * @var string
	 */
	protected $_action = 'index';
	
	/**
	 * The Controller 
	 * @var string
	 */
	protected $_controller = 'index';

	/**
	 * Optional Module 
	 * @var string
	 */
	protected $_module = null;
	
	/**
	 * Optional Format
	 * default is HTML
	 * @var string
	 */
	protected $_format = null;
	
	/**
	 * The Parameters
	 * @var array
	 */
	protected $_params = array();
	
	/**
	 * Sets the Action 
	 *
	 * @param string $action
	 * @return Request
	 */
	public function setAction($action)
	{
		$this->_action = (string) $action;
		return $this;
	}
	
	/**
	 * Get the Action 
	 *
	 * @return string
	 */
	public function getAction()
	{
		return $this->_action;
	}
	
	/**
	 * Sets the Controller 
	 *
	 * @param string $controller
	 * @return Request
	 */
	public function setController($controller)
	{
		$this->_controller = (string) $controller;
		return $this;
	}
	
	/**
	 * Get the Controller 
	 *
	 * @return string
	 */
	public function getController()
	{
		return $this->_controller;
	}
	
	/**
	 * Sets the Module 
	 *
	 * @param string $module
	 * @return Request
	 */
	public function setModule($module)
	{
		$this->_module = (string) $module;
		return $this;
	}
	
	/**
	 * Get the Module 
	 *
	 * @return string
	 */
	public function getModule()
	{
		return $this->_module;
	}
	
		/**
	 * Sets the Format 
	 *
	 * @param string $format
	 * @return Request
	 */
	public function setFormat($format)
	{
		$this->_format = (string) $format;
		return $this;
	}
	
	/**
	 * Get the Format 
	 *
	 * @return string
	 */
	public function getFormat()
	{
		if (!isset($this->_format)) {
			if ($format = $this->getParam('format')) {
				$this->_format = (string) $format;
			}
		}
		
		return $this->_format;
	}
	
	/**
	 * Get the Parameters 
	 *
	 * @return string
	 */
	public function getParams()
	{
		return $this->_params;
	}
	
	/** should not be used
	public function setParams($params)
	{
		require_once('Gen/Hash.php');
		$this->_params = new Gen_Hash($params);
		return $this;
	} */
	
	public function getParam($key, $default = null)
	{
		return (isset($this->_params[$key])&& ($this->_params[$key] !== '')) ? $this->_params[$key] : $default;
	}
	
	public function setParam($key, $value)
	{
		$this->_params[$key] = $value;
		return $this;
	}
	
	public function addParams($data)
	{
		$this->_params = array_merge($this->_params, $data);
		return $this;
	}
	
	public function getUrl()
	{
		return $_SERVER['REQUEST_URI'];
	}
	
	public function getServer()
	{
		return MS_SERVER_NAME;
	}
	
	public function getCurrentUrl(array $data = array(), $relative = true)
	{
		$url_infos = parse_url($_SERVER['REQUEST_URI']);
		$params = array();
		if (isset($url_infos['query'])) {
			parse_str($url_infos['query'], $params);
		}
		$params = array_merge($params, $data);
		return ($relative ? '' : ('http://' . $_SERVER['HTTP_HOST'])) . $url_infos['path'] . (count($params) ? ('?' . http_build_query($params)) : '');
	}
	
	static public function getIp()
	{
		$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		if(strpos($ip, ',')) {
			$ip = substr($ip, 0, strpos($ip, ','));
		}
		return $ip;
	}
	
	static public function isGoogleBot()
	{
		$ip = ip2long(self::getIp());
		return $ip >= 1123631104 && $ip <= 1123639295;
	}
	
	static public function getBrowser()
	{
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		
		// Opera/9.80 (Windows NT 5.1; U; fr) Presto/2.2.15 Version/10.00
		// Opera/9.80 (Windows NT 5.1; U; fr) Presto/2.2.15 Version/10.10
		
		// Mozilla/5.0 (Windows; U; Windows NT 5.1; fr; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 (.NET CLR 3.5.30729)
		
		// Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; MDDS; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)
		// Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; MDDS; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)
		
		// Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/532.0 (KHTML, like Gecko) Chrome/3.0.195.38 Safari/532.0
		// Mozilla/5.0 (Windows; U; Windows NT 5.1; fr-FR) AppleWebKit/528.16 (KHTML, like Gecko) Version/4.0 Safari/528.16
		
		switch(true) {
			case (strpos($user_agent, 'MSIE') !== false):
				$browser = 'IE';
				break;
				
			case (strpos($user_agent, 'AppleWebKit') !== false && strpos($user_agent, 'Chrome') !== false):
				$browser = 'CHROME';
				break;
				
			case (strpos($user_agent, 'AppleWebKit') !== false && strpos($user_agent, 'Chrome') === false):
				$browser = 'SAFARI';
				break;
				
			case (strpos($user_agent, 'Gecko') !== false && strpos($user_agent, 'KHTML') === false):
				$browser = 'FIREFOX';
				break;
				
			case (strpos($user_agent, 'Opera') !== false):
				$browser = 'OPERA';
				break;
				
			default:
				$browser = 'OTHER';
				break;
		}
		
		return $browser;
	}
	
	public function getMethod()
	{
		return $_SERVER['REQUEST_METHOD'];
	}
	
	public function isGet()
	{
		return ('GET' == $this->getMethod());
	}
	
	public function isPost()
	{
		return ('POST' == $this->getMethod());
	}
	
	public function isAjax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ? true : false;
	}
	
	public function toArray()
	{
		return array(
			'module' => $this->_module,
			'controller' => $this->_controller,
			'action' => $this->_action,
			'format' => $this->_format
		);
	}
}