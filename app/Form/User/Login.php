<?php
/** @see Gen_Form */
require_once('Gen/Form.php');

/**
 * @category   Together
 * @package    Together_Form
 */
class Form_User_Login extends Gen_Form
{
	protected $_name = 'user_login';

	public function init()
	{
		$this->addClass('std');
		
		$this->createElement('email', 'email')
		     ->setLabel('Email')
		     ->setHint('Email')
			 ->setRequired();
		
		$this->createElement('password', 'password')
		     ->setLabel('Password')
		     ->setHint('Password')
		     ->setRequired();
		
		$this->createElement('checkbox', 'persistent_login')
		     ->setLabel('Remember me');
		
		$this->createElement('submit', 'validate')
			 ->setLabel('Valider');
	}
}