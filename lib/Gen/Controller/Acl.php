<?php
class Gen_Controller_Acl
{
	const DEFAULT_CONTROLLER = 'all';
	
	const DEFAULT_ACTION = 'all';
	
	const DEFAULT_RESULT = 0;
	
	protected $_authorizations = array();
	
	public function permit($roles, $controllers, $actions)
	{
		$roles = (array) $roles;
		$controllers = (array) $controllers;
		$actions = (array) $actions;
		
		foreach($roles as $role) {
			$auth = isset($this->_authorizations[$role]) ? $this->_authorizations[$role] : array();
			foreach($controllers as $controller) {
				$controllerAuth = isset($auth[$controller]) ? $auth[$controller] : array();
				foreach($actions as $action) {
					$controllerAuth[$action] = 1;
				}
				$auth[$controller] = $controllerAuth;
			}
			$this->_authorizations[$role] = $auth;
		}
	}
	
	public function can($role, $controller, $action)
	{
		$auth = $this->getAuthorization($role);
		if(null === $auth) {
			Gen_Log::log('NO Role defined', 'Gen_Acl::can', 'warning');
			return self::DEFAULT_RESULT;
		}
		
		$result = self::DEFAULT_RESULT;
		foreach(array($controller, self::DEFAULT_CONTROLLER) as $c) {
			if(isset($auth[$c])) {
				$result = isset($auth[$c][$action])
						? $auth[$c][$action]
						: (isset($auth[$c][self::DEFAULT_ACTION])
							? $auth[$c][self::DEFAULT_ACTION]
							: self::DEFAULT_RESULT);
				if($result) {
					return $result;
				}
			}
		}
		Gen_Log::log('NO Controller defined', 'Gen_Acl::can', 'warning');
		return $result;
	}
	
	public function canRoute($role, $route)
	{
		$name = is_array($route) ? $route[0] : $route;

		$router = Gen_Controller_Front::getRouter();
		$route = $router->getRoute($name);
		
		return $this->allows($role, $route['default']['controller'], $route['default']['action']);
	}
	
	public function getAuthorization($role)
	{
		return isset($this->_authorizations[$role]) ? $this->_authorizations[$role] : null;
	}
	
	public function getAuthorizations()
	{
		return $this->_authorizations;
	}
	
	public function setAuthorizations($authorizations)
	{
		$this->_authorizations = $authorizations;
		return $this;
	}
}