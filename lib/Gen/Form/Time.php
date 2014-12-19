<?php
/** @see Gen_Form_Element */
require_once('Gen/Form/Element.php');

/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Form_Time extends Gen_Form_Element
{
	protected $_time;
		
	protected $_format = 'H:i:s';
	
	protected $_hourOffset = 5;
	
	public function setValue($time)
	{
		if(null != $time) {
			require_once('Gen/Date.php');
			$time = Gen_Date::create($time);
			$time = $time->format($this->_format);
		} else {
			$time = null;
		}
		return parent::setValue($time);
	}
	
	public function __construct(array $attributes = array())
	{
		parent::__construct('div', $attributes);
		$this->addClass('time');
	}
	
	public function render()
	{
		$name = $this->getName();

		$hour = new Gen_Form_Select();
		$hour->setName($name.'[hour]')
			->setEmptyLabel('hour')
			->setDatasource(array(0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23'))
		    ->disableWrapper();
		
		$minutes = array(); $t = 0;
		for($t; $t < 60; $t = $t + $this->_hourOffset) {
			$minutes[$t] = (string) $t;
		}
		
		$min = new Gen_Form_Select();
		$min->setName($name.'[min]')
			->setEmptyLabel('min')
			->setDatasource($minutes)		    
			->disableWrapper();
		
		$this->append($hour);
		$this->append($min);

		return parent::render();
	}
	
	public function enableTime($enableTime)
	{
		$this->_enable_time = (bool) $enableTime;
		return $this;
	}
}
