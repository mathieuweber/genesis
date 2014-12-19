<?php
/** @see Gen_Form_Input */
require_once('Gen/Form/Input.php');

/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Form_Email extends Gen_Form_Input
{
	protected $_type = 'email';
	
	public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
		$this->validate('email');
	}
}