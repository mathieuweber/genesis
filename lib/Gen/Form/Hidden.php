<?php
/** @see Gen_Form_Input */
require_once('Gen/Form/Input.php');

/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Form_Hidden extends Gen_Form_Input
{
	protected $_type = 'hidden';
	
	public function render()
	{
		return Gen_Dom_Element::render();
	}
}