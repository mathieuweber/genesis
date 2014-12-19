<?php
require_once('Controller/ApplicationController.php');

class BackofficeController extends ApplicationController
{
	protected $_layout = 'layout::backoffice';
	
	public function init()
	{
		parent::init();
		$this->addFilter('authentication', array('index', 'show', 'restore'));
		$this->addFilter('admin', array('index', 'show', 'new', 'create', 'edit', 'update', 'delete', 'restore'));
	}
	
	public function onProcessStart()
	{
		parent::onProcessStart();
		$this->addBreadCrumb(_t("Backoffice"), 'backoffice');
	}
	
	public function getForm($className = null)
	{
		$className = $className ? $className : 'Form_' . str_replace('Backoffice_', '', str_replace('Controller', '', get_class($this)));
		return parent::getForm($className);
	}
	
	/**********************************
	 *			 Filters			  *
	 **********************************/	
	public function adminFilter()
	{
		$user = $this->getCurrentUser();
		if(!$user->isAdmin()) {
			$this->redirect('default');
			return false;
		}
		return true;
	}
	
	/**********************************
	 *			 Actions			  *
	 **********************************/
	public function indexAction()
	{
	}
}