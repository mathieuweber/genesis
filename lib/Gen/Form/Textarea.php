<?php
/** @see Gen_Form_Input */
require_once('Gen/Form/Input.php');

/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Form_Textarea extends Gen_Form_Element
{
	protected $_tag = 'textarea';
	
	public function __construct(array $attributes = array())
	{
		parent::__construct('textarea', $attributes);
	}
	
	public function render()
	{
		$value = $this->resetAttribute('value');
		$this->setContent($value);
		return parent::render();
	}
}