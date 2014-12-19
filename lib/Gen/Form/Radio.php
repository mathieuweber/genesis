<?php
/** @see Gen_Form_Input */
require_once('Gen/Form/Input.php');

/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Form_Radio extends Gen_Form_Input
{
	protected $_type = 'radio';
	
	protected $_checkedValue = 1;
	
	protected $_uncheckedValue = null;
	
	public function init()
	{
		parent::setValue($this->_checkedValue);
	}
	
	public function setCheckedValue($checkedValue)
	{
		$this->_checkedValue = $checkedValue;
		parent::setValue($checkedValue);
		return $this;
	}
	
	public function setValue($value)
	{
		if ($value == $this->_checkedValue) {
			$this->check();
		} else {
			$this->uncheck();
		}
		return $this;
	}
	
	public function getValue()
	{
		return $this->isChecked() ? $this->_checkedValue : $this->_uncheckedValue;
	}
	
	public function isChecked()
	{
		return $this->getAttribute('checked');
	}
	
	public function check()
	{
		$this->setAttribute('checked' , true);
		return $this;
	}
	
	public function uncheck()
	{
		$this->resetAttribute('checked');
		return $this;
	}
}
