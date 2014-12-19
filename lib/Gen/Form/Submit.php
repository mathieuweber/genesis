<?php
/** @see Gen_Form_Input */
require_once('Gen/Form/Input.php');

/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Form_Submit extends Gen_Form_Input
{
    protected $_type = 'submit';

	protected $_warning;
    
    public function setLabel($label)
    {
    	parent::setValue($this->_enableTranslation ? _t($label) : $label);
    	return $this;
    }
	
	public function setValue($value)
	{
		return $this;
	}

	public function setWarning($text)
	{
		$this->_warning = $text;
		return $this;
	}

	public function getWarning()
	{
		return $this->_warning;
	}

	public function requireScript()
	{
		return "Gen.Form.Submit('".$this->getId()."', '".$this->getWarning()."');";
	}
}
