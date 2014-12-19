<?php
require_once('Gen/Str.php');

/**
 * @category   Gen
 * @package	Gen_Controller
 */
abstract class Gen_Controller_Action
{	 
	/**
	 * The Request
	 * @var Gen_Controller_Request
	 */
	protected $_request;
	
	/**
	 * The Response
	 * @var Gen_Controller_Response
	 */
	protected $_response;
	
	/**
	 * The current action
	 * @var string
	 */
	protected $_currentAction = 'undefined';
	
	/**
	  * the Controller name
	  * based on class name if none provided
	  * @var string
	  */
	protected $_name;
	
	/**
	 * The View
	 * @var Gen_View_Base
	 */
	protected $_view;
	
	/**
	 * The Layout key
	 * @var string
	 */
	protected $_layout;
	
	/**
	 * Indicator for rendering
	 * @var bool
	 */
	protected $_performRendering = true;
	
	/**
	 * Indicator for processing
	 */
	protected $_process = true;
	
	/**
	 * List of Filters
	 * @var array
	 */
	protected $_filters = array();
	
	/**
	 * List of Breadcrumbs
	 * @var array
	 */
	protected $_breadcrumbs = array();
	
	protected $_currentEvent;
	
	/**
	  * Format Action Method based on action key
	  *
	  * format is {:action|camelCase}Action
	  * 
	  * @param  string $action
	  * @return string $actionMethod
	  */
	public static function getActionMethod($action)
	{
		return Gen_Str::camelize($action) . 'Action';
	}
	
	public function setCurrentAction($action)
	{
		$this->_currentAction = (string) $action;
		return $this;
	}
	
	public function getCurrentAction()
	{
		return $this->_currentAction;
	}
	
	/**
	 * Gets the Controller Name
	 *
	 * @return string
	 */
	public function getName()
	{
		if (!isset($this->_name)) {
			$this->_name = (string) $this->getDefaultName();
		}
		return $this->_name;
	}
	
	public function getDefaultName()
	{
		$class = str_replace('Controller', '', get_class($this));
		return Gen_Str::underscore($class);
	}
   
	/**
	 * Sets the Request
	 *
	 * @param  Gen_Controller_Request $request
	 * @return Controller
	 */
	public function setRequest(Gen_Controller_Request $request)
	{
		$this->_request = $request;
		return $this;
	}
	
	/**
	 * Gets the Request
	 *
	 * @return Gen_Controller_Request $request
	 */
	public function getRequest()
	{
		if (!isset($this->_request)) {
			require_once ('Gen/Controller/Request.php');
			$this->_request = new Gen_Controller_Request();
		}
		return $this->_request;
	}
	
	/**
	 * Sets the Response
	 *
	 * @param  Gen_Controller_Response $response
	 * @return Controller
	 */
	public function setResponse(Gen_Controller_Response $response)
	{
		$this->_response = $response;
		return $this;
	}
	
	/**
	 * Gets the Response
	 *
	 * @return Gen_Controller_Response $response
	 */
	public function getResponse()
	{
		if (!isset($this->_response)) {
			require_once ('Gen/Controller/Response.php');
			$this->_response = new Gen_Controller_Response();
		}
		return $this->_response;
	}
	
	/**
	 * Retrieve a given Parameter by key
	 *
	 * @return mixed
	 */
	public function getParam($key, $default = null)
	{
		return $this->getRequest()->getParam($key, $default);
	}
	
	/**
	 * Gets all Parameters
	 *
	 * @return Gen_Hash $params
	 */
	public function getParams()
	{
		return $this->getRequest()->getParams();
	}
	
	/**
	 * Gets current Url
	 *
	 * @return string
	 */
	public function getCurrentUrl(array $data = array(), $relative = true)
	{
		return $this->getRequest()->getCurrentUrl($data, $relative);
	}
	
	/**
	 * Sets the View
	 *
	 * @param Gen_View_Base $view
	 * @return Controller
	 */
	public function setView(Gen_View_Base $view)
	{
		$this->_view = $view;
		return $this;
	}
	
	/**
	 * Gets the View
	 *
	 * @return Gen_View_Base $view
	 */
	public function getView()
	{
		if (!isset($this->_view)) {
			require_once ('Gen/View/Base.php');
			$this->_view = new Gen_View_Base();
		}
		return $this->_view;
	}
	
	/**
	 * Sets the Layout key
	 *
	 * @param layout
	 * @return Controller
	 */
	public function setLayout($layout)
	{
		$this->_layout = (string) $layout;
		return $this;
	}
	
	/**
	 * Gets the Layout
	 *
	 * @return Gen_View_Layout $layout
	 */
	public function getLayout()
	{
		return $this->_layout;
	}
	
	/**
	 * Disables the layout rendering
	 * @return Gen_Controller_Action
	 */
	public function disableLayout()
	{
		$this->_layout = null;
		return $this;
	}
	
	public function performRendering()
	{
		return $this->_performRendering;
	}
	
	public function enableRendering()
	{
		$this->_performRendering = true;
		return $this;
	}
	
	public function disableRendering()
	{
		$this->_performRendering = false;
		return $this;
	}
	
	public function stopProcessing()
	{
		$this->_process = false;
		return $this;
	}
	
	/************************************
	 *				Session				*
	 ************************************/
	public function setSession($key, $value)
	{
		if(!isset($_SESSION['App_Session_' . APP_KEY . '_' . APP_VERSION])) {
			$_SESSION['App_Session_' . APP_KEY . '_' . APP_VERSION] = array();
		}
		$_SESSION['App_Session_' . APP_KEY . '_' . APP_VERSION][$key] = $value;
	}
	
	public function unsetSession($key)
	{
		$value = null;
		if(isset($_SESSION['App_Session_' . APP_KEY . '_' . APP_VERSION])) {
			if(isset($_SESSION['App_Session_' . APP_KEY . '_' . APP_VERSION][$key])) {
				$value = $_SESSION['App_Session_' . APP_KEY . '_' . APP_VERSION][$key];
				unset($_SESSION['App_Session_' . APP_KEY . '_' . APP_VERSION][$key]);
			}
			$_SESSION['App_Session_' . APP_KEY . '_' . APP_VERSION][$key] = null;
		}
		return $value;
	}
	
	public function getSession($key, $default = null)
	{
		if(isset($_SESSION['App_Session_' . APP_KEY . '_' . APP_VERSION]) && isset($_SESSION['App_Session_' . APP_KEY . '_' . APP_VERSION][$key])) {
			return $_SESSION['App_Session_' . APP_KEY . '_' . APP_VERSION][$key];
		}
		return $default;
	}
	
	public function getPersistentParam($key, $default = null)
	{
		if($value = $this->getParam($key)) {
			$this->setSession($key, $value);
		}
		return $this->getSession($key, $value);
	}
	
	/************************************
	 *				Cookie				*
	 ************************************/
	public function getCookie($name = 'Gen_Cookie')
	{
		if (!isset($this->_cookies[$name])) {
			require_once('Gen/Http/Cookie.php');
			$this->_cookies[$name] = new Gen_Http_Cookie($name);
		}
		return $this->_cookies[$name];
	}
   
	public function assignCookie($key, $value)
	{
		$this->getCookie()->setParam($key, $value);
		return $this;
	}
	
	/************************************
	 *			  Filters			 *
	 ************************************/
	public function addFilter($name, array $actions, $controller = null)
	{
		if(($controller === null) || ($controller == $this->getName()))
		{
			$name = (string) $name;
			if (!isset($this->_filters[$name])) {
				$this->_filters[$name] = $actions;
			} else {
				$this->_filters[$name] = array_merge($this->_filters[$name], $actions);
			}
		}
		return $this;
	}
	
	public function removeFilter($name, array $actions)
	{
		$name = (string) $name;
		if (isset($this->_filters[$name])) {
			$this->_filters[$name] = array_diff($this->_filters[$name], $actions);
		}
		return $this;
	}
	
	public function getFilters()
	{
		return $this->_filters;
	}
	/*********************************
	 *			 Breadcrumb			 *
	 *********************************/
	public function addBreadcrumb($label, $route = null)
	{
		$this->_breadcrumbs[] = array(
			'label' => $label,
			'route' => $route
		);
		return $this;
	}
	
	public function getBreadcrumbs()
	{
		return $this->_breadcrumbs;
	}
	
	/**
	 * Constructor
	 *
	 * calls {@link init()}
	 */
	public function __construct()
	{
		$this->init();
	}
	
	/**
	 * Init function to be implemented by Action Controller
	 */
	public function init() {}
	
	public function onProcessStart(){}
	
	public function onProcessEnd(){}
	
	public function onActionStart(){}
	
	public function onActionEnd(){}
	
	public function onRenderStart(){}
	
	public function process(Gen_Controller_Request $request, Gen_Controller_Response $response)
	{
		$this->_request = $request;
		$this->_response = $response;
		
		Gen_Log::log('on Process Start', 'Gen_Controller_Action::process');
		$this->onProcessStart();
		
		$action = $this->getRequest()->getAction();
		
		/** filter */
		if ($this->_process) {
			Gen_Log::log($this->getFilters(), 'Filters');
			$this->filter($action);
		}
		
		/** execute */
		if ($this->_process) {
			$this->execute($action);
		}
		
		$this->onProcessEnd();
		return $this->getResponse();
	}
	
	public function filter($action)
	{
		foreach ($this->getFilters() as $filterName => $filterActions) {
			/** should we filter ? */
			if (in_array($action, $filterActions)) {
				/** call filter if exists */
				$filterMethod = Gen_Str::camelize($filterName) . 'Filter';
				Gen_Log::log($filterMethod, 'filter');
				if (method_exists($this, $filterMethod)) {
					/** filters applied */
					if (!$this->$filterMethod($action)) {
						$this->stopProcessing();
						return false;
					}
				} else {
					throw new Exception("Unknown filter $filterMethod in " . get_class($this) . "::filter");
				}
			}
		}
		return true;
	}
	
	public function execute($action)
	{
		Gen_Log::log($action, 'Gen_Controller_Action::execute');
		
		$this->setCurrentAction($action);
		$this->onActionStart();
		
		if ($this->_process) {
			$method = (string) $this->getActionMethod($action);
			
			if (method_exists($this, $method)) {
				/** execute action */
				$this->$method();
				
				/** render view */
				if ($this->performRendering() && $this->_process) {
					$this->render();
				}
			} else {
				require_once('Gen/Controller/Exception.php');
				throw new Gen_Controller_Exception(
					"Cannot execute given action: $method in " . get_class($this) . "::execute()"
				);
			}
		}
		
		if ($this->_process) {
			$this->onActionEnd();
		}
	}
	
	public function render($request = null, $controller = null, $module = null)
	{
		if(!($request instanceof Gen_Controller_Request)) {
			$action = $request;
			$request = $this->getRequest();
			if ($action !== null) {
				$request->setAction($action);
			}
			if ($controller !== null) {
				$request->setController($controller);
			}
			if ($module !== null) {
				$request->setModule($module);
			}
		}
		$this->disableRendering();
		Gen_Log::log('onRenderStart', 'Gen_Controller_Action::render');
		$this->onRenderStart();
		
		Gen_Log::log('rendering', 'Gen_Controller_Action::render');
		/** sets defaults */
		$options = $request->toArray();
		
		/** gestion du format de rendu */
		switch($options['format']) {
			case 'rss':
				$this->getResponse()->setContentType('text/xml; charset=utf-8');
				$this->setLayout('layout::rss');
				break;
				
			case 'xml':
				$this->getResponse()->setContentType('text/xml; charset=utf-8');
				$this->disableLayout();
				break;
			
			case 'json':
				$this->getResponse()->setContentType('application/json; charset=utf-8');
				$this->disableLayout();
				break;
			
			case 'txt':
				$this->getResponse()->setContentType('text/plain; charset=utf-8');
				$this->disableLayout();
				break;
			
			case 'ajax':
				$this->disableLayout();
				
			case 'html':
			default:
				$options['format'] = null;
				$this->getResponse()->setContentType('text/html; charset=utf-8');
				break;
		}
		
		if($request->getParam('disable_layout', false)) {
			$this->disableLayout();
		}
		
		$view = $this->getView();
		$view->setLayout($this->_layout);
		$view->assign('breadcrumbs', $this->getBreadcrumbs());
		$content = $view->render($options);
		/** appends the result to the response body */
		$this->getResponse()->appendBody($content);
		return $content;
	}
	
	public function redirect($name, array $params = array(), $relative = true)
	{
		return $this->redirectUrl($this->url($name, $params, $relative));
	}
	
	public function redirectUrl($url,$params = array())
	{
		require_once('Gen/Http/Request.php');
		/** @hack */
		if(isset($_GET['show_log'])) {
			$params['show_log'] = true;
		}
		$url = Gen_Http_Request::buildUrl($url, $params);

		$this->disableRendering();
		$this->stopProcessing();
		
		/** @hack */ if(isset($_GET['stop_processing'])) { return false; }
		
		$this->getResponse()->redirect($url);
	}
	
	public function permanentRedirect($name, array $params = array(), $relative = true)
	{
		return $this->permanentRedirectUrl($this->url($name, $params, $relative));
	}
	
	public function permanentRedirectUrl($url)
	{
		$this->disableRendering();
		$this->stopProcessing();
		
		/** @hack */ if(isset($_GET['stop_processing'])) { return false; }
		
		$this->getResponse()->permanentRedirect($url);
		
	}
	
	public function getBackUrl($default = null)
	{
		return $this->getParam('redirect', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $default);
	}
	
	public function redirectBack($name = 'default', array $params = array())
	{
		$this->redirectUrl($this->getBackUrl($this->url($name, $params)));
	}
	
	/**
	 * Helper for Router::url()
	 *
	 * @param  string $name of the Route
	 * @param  array $data to build the url
	 * @return string the corresponding url
	 */
	public function url($name = 'default', array $data = array(), $relative = true)
	{
		require_once('Gen/Controller/Front.php');
		return Gen_Controller_Front::getInstance()
									->getRouter()
									->url($name, $data, $relative);
	}
	
	public function viewData($key, $value)
	{
		$this->getView()->assign($key, $value);
		return $this;
    }
    
    public function setFlash($key, $value)
    {
        Gen_Session_Flash::getInstance()->set($key, $value);
        return $this;
    }
    
    public function getFlash($key, $default = null)
    {
        return Gen_Session_Flash::getInstance()->get($key, $default);
    }
    
    public function setMessage($message, $level = 'info', $params = null, $translate = true)
    {
        Gen_Session_Flash::getInstance()->set('Gen_Controller_Message', $translate ? _t($message, $params) : $message);
        Gen_Session_Flash::getInstance()->set('Gen_Controller_Message_Level', $level);
        return $this;
    }
    
    public function setWarning($message)
    {
        $this->setMessage($message, 'warning');
        return $this;
    }

	public function setError($message)
	{
		$this->setMessage($message, 'error');
		return $this;
	}
	
	public function getMessage($default = null)
	{
		return Gen_Session_Flash::getInstance()->get('Gen_Controller_Message', $default);
	}
	
	public function getMessageLevel($default = 'info')
	{
		return Gen_Session_Flash::getInstance()->get('Gen_Controller_Message_Level', $default);
	}

	/************************************
	 *			   Form			   *
	 ************************************/
	public function getForm($className = null)
	{
		require_once('Gen/Repository.php');
		$repo = Gen_Repository::getInstance('Gen_Controller_Action_Forms');
		
		$className = $className ? $className : 'Form_' . str_replace('Controller', '', get_class($this));
		
		if (!$repo->get($className)) {
			$fileName = str_replace('_', '/', $className) . '.php';
			require_once ($fileName);
			$serializedForm = $this->getFlash($className);
			$form = $serializedForm ? unserialize($serializedForm) : new $className();
			$repo->set($className, $form);
		}
		return $repo->get($className);
	}
	
	public function setForm(Gen_Form $form)
	{
		$this->setFlash(get_class($form), serialize($form));
		return $this;
	}
	
	/***********************************
	 *				EVENTS			   *
	 ***********************************/
	public function fire($name,$params = array())
	{
		return Gen_Controller_Front::fire($name, $params);
	}
	
	public function processEvent($action, Gen_Controller_Event $event)
	{
		$listener = $this->getListenerMethod($action);
		$this->setEvent($event);
		return $this->$listener();
		// try {
			// return $this->$listener();	
		// } catch (Exception $e) {
			// Gen_Controller_Front::sendExceptionMail($e);
			// Gen_Log::log(sprintf('Exception for listener `%s` : %s',$listener, $e->getMessage()), __CLASS__ . '::process', 'error');
			// return false;
		// }
	}
	
	public static function getListenerMethod($action)
	{
		return Gen_Str::camelize($action) . 'Listener';
	}
	
	public function getEvent()
	{
		return $this->_currentEvent;
	}

	public function setEvent(Gen_Controller_Event $event)
	{
		$this->_currentEvent = $event;
	}
}
