<?php
/** @see Gen_Form_Element */
require_once('Gen/Form/Element.php');
require_once('Gen/Date.php');

/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Form_Date extends Gen_Form_Element
{
	protected $_date;
	protected $_enable_time = false;
	protected $_firstYear = 1990;
	protected $_lastYear = 2020;
	
	protected $_format = 'Y/m/d H:s';
	
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
		parent::__construct('div', $attributes);
		$this->addClass('date');
	}
	
	public function render()
	{
		$name = $this->getName();

		$day = new Gen_Form_Select();
		$day->setName($name.'[day]')
			->setEmptyLabel('day')
			->setDatasource(array(1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29', 30 => '30', 31 => '31'))
		    ->disableWrapper();
		
		$month = new Gen_Form_Select();
		$month->setName($name.'[month]')
			->setEmptyLabel('month')
			->setDatasource(array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'Jully', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'))		    
			->disableWrapper();
		
		$this->append($day);
		$this->append($month);

		return parent::render();
	}
	
	public function enableTime($enableTime)
	{
		$this->_enable_time = (bool) $enableTime;
		return $this;
	}
}
