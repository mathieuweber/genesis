<?php
require_once('Gen/Controller/Action.php');

class ApplicationController extends Gen_Controller_Action
{
	protected $_layout = 'layout::default';
	
	private $_currentUser;
	
	public function authenticationFilter()
	{
		$user = $this->getCurrentUser();
		if(!$user->isAuthenticated()) {
			$this->redirect('user_login', array('redirect'=> $_SERVER['REQUEST_URI']));
			return false;
		}
		return true;
	}
	
	public function authorizationFilter()
	{
		$user = $this->getCurrentUser();
		$controller = $this->getRequest()->getController();
		$action = $this->getRequest()->getAction();
		
		if(!$user->can($controller, $action)) {
			$this->getResponse()->unauthorized();
			return false;
		}
		return true;
	}
	
	public function confirmFilter()
	{
		$confirm = $this->getParam('confirm', false);
		if($confirm == false) {
			$this->redirect('confirm', array('request' => $this->getCurrenturl(),'redirect'=> $this->getBackUrl()));
			return false;
		}
		return true;
	}
	
	public function init()
	{
		parent::init();
		$this->addFilter('authentication', array('new', 'create', 'update', 'edit', 'delete'));
		$this->addFilter('confirm', array('delete'));
	}
	
	public function getCurrentUser()
	{
		if (!isset($this->_currentUser)) {
			require_once('Model/User.php');
			if (isset($_SESSION['Application_User_' . APP_KEY . '_' . APP_VERSION])) {
				$user = new User($_SESSION['Application_User_' . APP_KEY . '_' . APP_VERSION]);
			} else {
				$user = new User();
			}
			$this->_currentUser = $user;
		}
		
		return $this->_currentUser;
	}
	
	public function setCurrentUser(User $user)
	{
		/** prevents fishing, or should do so*/
		if (!headers_sent()) {
			session_regenerate_id();
		}
		$_SESSION['Application_User_' . APP_KEY . '_' . APP_VERSION] = $user->toArray();
		$this->_currentUser = $user;
	}
	
	public function unsetCurrentUser()
	{
		unset($_SESSION['Application_User_' . APP_KEY . '_' . APP_VERSION]);
		$_SESSION['Application_User_' . APP_KEY . '_' . APP_VERSION] = null;
		$this->_currentUser = null;
	}
	
	public function onProcessStart()
	{
		$this->addBreadCrumb(_t("Home"), 'default');
		$this->manageI18n();
		$this->managePersistentLogin();
	}
	
	public function breadCrumbFilter()
	{
		return true;
	}
	
	public function onRenderStart()
	{
		$currentUser = $this->getCurrentUser();
		$this->viewData('currentUser', $currentUser);
	}
	
	/************************************
     *          Security Token          *
     ************************************/
    public function managePersistentLogin()
    {
		$user = $this->getCurrentUser();
		if (!$user->isAuthenticated()) {
			$cookie = $this->getCookie('persistent_login');
			$email = $cookie->getParam('email');
			if ($email) {
				require_once('Bo/User.php');
				$user = Bo_User::findByEmail($email);	 
				if (($user instanceof User)) {
					if (Bo_User::connect($user)){
						$this->setCurrentUser($user);
						$this->setPersistentLoginCookie($user);
						$this->fire('user_authenticated',array());
					}
				}
			}
		}
	}
	
	public function setPersistentLoginCookie(User $user)
	{
		$cookie = $this->getCookie('persistent_login');
		$cookie->setParam('email', $user->getEmail());
	}
	
	public function unsetPersistentLoginCookie()
	{
		$cookie = $this->getCookie('persistent_login');
		$cookie->unsetParam('email');
	}
	
	/******************************
	 *            I18n            *
	 ******************************/
	public function manageI18n()
	{
		$lang = $this->getPersistentParam('lang', APP_DEFAULT_LANG);
		$previousLang = Gen_I18n::getLocale();
		if($previousLang != $lang) {
			Gen_I18n::setLocale($lang);
		}
	}
}