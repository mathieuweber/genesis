<?php
/** @see Gen_Form_Element */
require_once('Gen/Form/Element.php');
require_once('Gen/Date.php');

/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Form_Date extends Gen_Form_Textbox
{
	protected $_date;
	protected $_enable_time = false;
	protected $_firstYear;
	protected $_lastYear;
	
	protected $_format = 'Y/m/d H:i';
	
	public function setValue($date)
	{
		if(null != $date) {
			$date = Gen_Date::create($date);
			$date = $date->format($this->_format);
		} else {
			$date = null;
		}
		return parent::setValue($date);
	}
	
	public function __construct(array $attributes = array())
	{
		$this->_icon = 'calendar';
		parent::__construct($attributes);
		$this->addClass('date');
	}
	
	public function render()
	{
		$this->appendScript('$(function() {$( "#'. $this->getId() .'" ).datepicker({dateFormat : \'yy-mm-dd\'});});');
		return parent::render();
	}
	
	public function enableTime($enableTime)
	{
		$this->_enable_time = (bool) $enableTime;
		return $this;
	}
}
