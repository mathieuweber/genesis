<?php
require_once('ApplicationController.php');

class ErrorController extends ApplicationController
{
	public function errorAction()
	{
		$this->viewData('backUrl', $this->getBackUrl());
	}
	
	public function notFoundAction(){}
	
	public function warningAction(){}

	public function unauthorizedAction()
	{
		$this->viewData('backUrl', $this->getBackUrl());
	}
	
	public function confirmAction()
	{
		$redirect = $this->getParam('redirect');
		$requestUrl = $this->getParam('request');
		$confirmUrl = $requestUrl.'?'.http_build_query(array('confirm' => 'true', 'redirect' => $redirect));
		
		$this->viewData('confirmUrl', $confirmUrl);
		$this->viewData('cancelUrl', $redirect);
	}
}