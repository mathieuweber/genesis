<?php
require_once('Gen/Session/Flash.php');
/**
 * The Front Controller
 *
 * the Front Controller managers incoming Requests and render the final Response
 *
 * Use the static method {@link run()} to start the Front Controller process
 *
 * The Front Controller implements the Singleton pattern
 * to ensure that only one process is performed
 *
 * The incoming Request is passed to the Router
 * to determine the first Controller Action to process
 *
 * Finaly the Response is rendered
 *
 * @category   Gen
 * @package	Gen_Controller
 */
class Gen_Controller_Front
{		
	public static $env = 'localhost';
	
	public static $debug = false;
	
	public static $appName = 'Gen';
	
	public static $appMail = null;
	
	/**
	 * the Controllers directory
	 * defaulted to ./application/Controller/
	 * @var string
	 */
	public static $controllerDir = './application/Controller/';
	
	protected static $_viewDir;
	
	/**
	 * The Singleton instance
	 * @var Gen_Controller_Front
	 */
	protected static $_instance;
	
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
	 * The Router
	 * @var Gen_Controller_Router
	 */
	protected $_router;
	
	/**
	 * The Cookie
	 * @var Gen_Http_Cookie
	 */
	protected $_cookie;
	
	protected $_handleError = true;
	
	protected $_eventDispatcher;
	
	/**
	 * Constructor
	 *
	 * Instantiate using {@link getInstance()}; event handler is a singleton
	 * object.
	 *
	 * @return void
	 */
	protected function __construct()
	{
	}

	/**
	 * Enforce singleton; disallow cloning 
	 * 
	 * @return void
	 */
	private function __clone()
	{
	}

	/**
	 * Singleton instance
	 *
	 * @return Gen_Controller_Front
	 */
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
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
	 * Gets the Router
	 *
	 * defaults it to Gen_Controller_Route if none provided
	 *
	 * @return Gen_Controller_Route
	 */
	public function getRouter()
	{
		if (!isset($this->_router)) {
			require_once('Gen/Controller/Router.php');
			$this->_router = new Gen_Controller_Router();
		}
		return $this->_router;
	}
	
	/**
	 * Sets the Router
	 *
	 * @param  Gen_Controller_Router
	 * @return Gen_Front_Controller
	 */
	public function setRouter(Gen_Controller_Router $router)
	{
		$this->_router = $router;
		return $this;
	}
	
	/**
	 * Set base url
	 *
	 * @param  string $baseUrl
	 * @return Gen_Controller_Front
	 */
	public function setBaseUrl($baseUrl)
	{
		$this->getRouter->setBaseUrl($baseUrl);
		return $this;
	}
	
	/**
	 * Sets the Cookie
	 *
	 * @param  Gen_Http_Cookie $cookie
	 * @return Controller
	 */
	public function setCookie(Gen_Http_Cookie $cookie)
	{
		$this->_cookie = $cookie;
		return $this;
	}
	
	/**
	 * Gets the Cookie
	 *
	 * @return Gen_Http_Cookie $cookie
	 */
	public function getCookie()
	{
		if (!isset($this->_cookie)) {
			require_once ('Gen/Http/Cookie.php');
			$this->_cookie = new Gen_Http_Cookie();
		}
		return $this->_cookie;
	}
	
	public static function setViewDir($dir)
	{
		self::$_viewDir = $dir;
		require_once('Gen/View/Base.php');
		Gen_View_Base::$defaultBaseDir = $dir;
	}
	
	public function setHandleError($handleError)
	{
		$this->_handleError = (bool) $handleError;
	}
	
	/**
	 * Run the Front Controller process
	 *
	 * add optionals controller directory
	 *
	 * @param  string|array $controllerDir
	 * @return Gen_Controller_Response
	 */
	public static function run()
	{
		$frontController = self::getInstance();
		$request  = $frontController->getRequest();
		$response = $frontController->getResponse();
		
		return $frontController->process($request, $response);
	}
	
	public function process(Gen_Controller_Request $request, Gen_Controller_Response $response)
	{
		try {
			/** initiate */
			Gen_Log::log('initiate', 'Gen_Controller_Front::process');
			$this->initiate();
			/** route */
			Gen_Log::log('route', 'Gen_Controller_Front::process');
			if($this->getRouter()->route($request)) {
				/** process */
				Gen_Log::log($request->toArray(), 'Request');
				$response = $this->dispatch($request, $response);
			} else {
				Gen_Log::log('No route matched', 'Gen_Controller_Front::process');
				$response->notFound();
			}
				
			/** handle Failure */
			if (!$request->isAjax()) {
				Gen_Log::log('handleFailure', 'Gen_Controller_Front::process');
				$response = $this->handleFailure($request, $response);
			}
			/** send response */
			Gen_Log::log('Send Response', 'Gen_Controller_Front::process');
			$response->send();
			
			/** finalize */
			$this->finalize();
			return $response;
			
		} catch (Exception $e) {
			/** send mail to support */
			try {
				self::sendExceptionMail($e);
			} catch(Exception $mailE) { }
			/** send a 505 internal error response */
			$response = $this->getResponse()->error();
			if (!$request->isAjax()) {			
			
				$request = $this->getRequest()
							->setModule('')
							->setController('error')
							->setAction('error')
							->setFormat('html');
							
				if(self::$debug) {
					$_GET['show_log'] = true;
					$msg = $e->getMessage() . "\n\nin file " . $e->getFile() . "\non line " . $e->getLine() . "\n\nSTACK TRACE:\n\n" . $e->getTraceAsString();
					Gen_Log::log('<pre>'.$msg.'</pre>', 'Gen_Controller_Front::process', 'error');
				}
				$response = $this->dispatch($request, $response);
			}
			$response->send();
		}
	}
	
	/**
	 * Dispatches a request
	 * 
	 * Determines the Controller and let it process the request
	 *
	 * @param  Gen_Controller_Request $request
	 * @param  Gen_Controller_Response $response
	 * @return Gen_Controller_Response $response
	 */
	public function dispatch(Gen_Controller_Request $request, Gen_Controller_Response $response)
	{
		$controllerName = $request->getController();
		$moduleName = $request->getModule();
		
		require_once('Gen/ClassLoader.php');
		$className = Gen_ClassLoader::loadClass($controllerName, $moduleName,'Controller', self::$controllerDir);
		if (!class_exists($className)) {
			Gen_Log::log('Class Not Found: '. $className, 'Gen_Controller_Front::dispatch', 'warning');
			return $response->notFound();
		}
		$controller = new $className();		
		$response = $controller->process($request, $response);
		return $response;
	}
	
	public function handleFailure(Gen_Controller_Request $request, Gen_Controller_Response $response)
	{
		if (!$response->hasJsonHeader()) {
			switch ($response->getStatus()) {
				
				/** OK, on a white list base */
				case 200:
				case 201:
				case 202:
				case 301:
				case 302:
				case 304:
					break;
				
				/** warning */
				case 400:
				case 405:
				$request
						->setController('error')
						->setAction('warning')
						->setModule('')
						->setFormat('');
					Gen_Log::log('Redirect > Warning', 'Gen_Controller_Front::handleFailure');
					$response = $this->dispatch($request, $response);
					break;
					
				/** unauthorized */
				case 401:
					$request
						->setController('error')
						->setAction('unauthorized')
						->setModule('')
						->setFormat('');
					Gen_Log::log('Redirect > Unauthorized', 'Gen_Controller_Front::handleFailure');
					$response = $this->dispatch($request, $response);
					break;
					
				/** not found */
				case 404:
					$request
						->setController('error')
						->setAction('not_found')
						->setModule('')
						->setFormat('');
					Gen_Log::log('Redirect > Not Found', 'Gen_Controller_Front::handleFailure');
					$response = $this->dispatch($request, $response);
					break;
				
				case 500:
				default:
					 $request
						->setController('error')
						->setAction('error')
						->setModule('')
						->setFormat('');
					Gen_Log::log('Redirect > Error', 'Gen_Controller_Front::handleFailure');
					$response = $this->dispatch($request, $response);
					break;
			}
		}
		return $response;
	}
	
	public function initiate()
	{
		session_start();
		require_once('Gen/Session/Flash.php');
		Gen_Session_Flash::getInstance()->load();
	}
	
	public function finalize()
	{
		Gen_Session_Flash::getInstance()->save();
	}
	
	public static function sendExceptionMail(Exception $e)
	{
		$msg = $e->getMessage() . "\n\n"
			 . ' in file ' . $e->getFile() . "\n"
			 . ' on line '  . $e->getLine()
			 . "\n\n"
			 . "STACK TRACE:\n\n" . $e->getTraceAsString();
		return self::sendMail($msg);
	}
	
	public static function sendMail($msg, $level='Error')
	{
		Gen_Log::log($msg, 'msg in FrontController::sendMail', 'warning');
		if(SEND_MAIL === true)
		{
			$email = '"'. self::$appName .'"<'. self::$appMail .'>';
		
			$headers = 'From: '. $email ."\n"
					 . 'Reply-To: '. $email ."\n"
					 . "X-Mailer: Gen Mailer\n"
					 . "MIME-Version: 1.0\n"
					 . "Content-type: text/plain; charset=UTF-8\n";
			
			$msg .= "\n\n"
				 . '  *****************************************************' . "\n\n"
				 . '  >>> Include Path: ' . ini_get('include_path') . "\n\n"
				 . '  >>> GET:' . "\n\n" . print_r($_GET, true) . "\n\n"
				 . '  >>> POST:' . "\n\n" . print_r($_POST, true) . "\n\n"
				 . '  >>> FILES:' . "\n\n" . print_r($_FILES, true) . "\n\n"
				 . '  >>> SESSION:' . "\n\n" . print_r($_SESSION, true) . "\n\n"
				 . '  >>> SERVER:' . "\n\n" . print_r($_SERVER, true);
			
			$title = '[' . self::$appName . '][' . self::$env .'] '. $level;
			
			/** @todo : mail with beanstalk */
			require_once('PHPMailer/class.phpmailer.php');
			$mail = new PHPMailer();
			// $mail->IsSMTP(); // send via SMTP
			// $mail->SMTPAuth = true; // turn on SMTP authentication
			// $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
			// $mail->Username = ''; // SMTP username
			// $mail->Password = ''; // SMTP password
			// $mail->Host = '';
			// $mail->Port = 465;
		
			$mail->From = self::$appName;
			$mail->FromName = 'Support';
			$mail->AddAddress(self::$appMail, 'Support');
			$mail->AddReplyTo(self::$appMail, 'Support');
			$mail->Subject = $title;
			$mail->Body = $msg;
			
			try {
				if (!$mail->Send()) {
					Gen_Log::log($mail->ErrorInfo, 'PhpMailer', 'error');
				}
			} catch (Exception $e) {
				Gen_Log::log($e->getMessage(), 'PhpMailer', 'error');
			}
		}
		return true;
	}
	
	public static function executeAction($action, $controller = null, $params = array())
	{
		return self::getInstance()->getDispatcher()->executeAction($action, $controller, $params);
	}
	
	
	/***********************************
	 *				EVENTS			   *
	 ***********************************/
	public static function fire($name, $params = array())
	{
		require_once('Gen/Controller/Event.php');
		$event = new Gen_Controller_Event($name, $params);
		return self::getInstance()->fireEvent($event);
	}
	
	public function fireEvent(Gen_Controller_Event $event)
	{
		$listeners = $this->getEventDispatcher()->getListeners($event->getName());
		Gen_Log::log(sprintf('event : %s | listeners : %s', $event->getName(),json_encode($listeners)), 'Gen_Controller_Front::fireEvent', 'info');
		
		if(empty($listeners)) {
			Gen_Log::log('Event not listened: ' . $event->getName() , 'Gen_Controller_Front::fireEvent', 'warning');
		} else {
			foreach ($listeners as $listener) {
				$this->getEventDispatcher()->notify($listener, $event);
			}
		}
		
		return $event;
	}

	public function getEventDispatcher()
	{
		if (!isset($this->_eventDispatcher)) {
			require_once('Gen/Controller/Event/Dispatcher.php');
			$this->_eventDispatcher = new Gen_Controller_Event_Dispatcher();
		}
		return $this->_eventDispatcher;
	}
	
	public function setEventDispatcher(Gen_Controller_Event_Dispatcher $dispatcher)
	{
		$this->_eventDispatcher = $dispatcher;
		return $this;
	}
	
	/**
	 * Notifies all listeners of a given event until one returns a non null value.
	 * Used in a delegation chain
	 * @param  Event $event A Event instance
	 *
	 * @return Event The Event instance
	 */
	public function fireUntil(Gen_Controller_Event $event)
	{
		foreach ( $this->getEventDispatcher()->getListeners($event->getName()) as $listener) {
			if ($this->getEventDispatcher()->notify($listener, $event)) {
				$event->setProcessed(true);
				break;
			}
		}

		return $event;
	}
	
	/***********************************
	 *				ACL				   *
	 ***********************************/

	/**
	 * Gets the Acl
	 *
	 * defaults it to Gen_Controller_Acl if none provided
	 *
	 * @return Gen_Controller_Acl
	 */
	public function getAcl()
	{
		if (!isset($this->_acl)) {
			require_once('Gen/Controller/Acl.php');
			$this->_acl = new Gen_Controller_Acl();
		}
		return $this->_acl;
	}
	
	/**
	 * Sets the Acl
	 *
	 * @param  Gen_Controller_Acl
	 * @return Gen_Front_Controller
	 */
	public function setAcl(Gen_Controller_Acl $acl)
	{
		$this->_acl = $acl;
		return $this;
	}
}
