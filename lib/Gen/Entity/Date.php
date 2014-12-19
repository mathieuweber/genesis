<?php
require_once('Gen/Date.php');

class Gen_Entity_Date
{
	protected $_date;
	
	protected $_timezone = false;
	
	protected $_lang = null;
	
	public function __construct($date = null)
	{
		if(null !== $date) {
			$this->_date = Gen_Date::create($date);
		}
	}
	
	public function update($date)
	{
		if(null == $date) {
			$this->_date = null;
		} else {
			$this->_date = Gen_Date::create($date);
		}
		return $this;
	}
	
	public function toTimestamp()
	{
		return strtotime($this->_date->format('Y-m-d h:i:s'));
	}
	
	public function format($format = 'Y-m-d H:i')
	{
		if(null === $this->_date) {
			return null;
		}
		if($format == 'smart_date') {
			return Gen_Date::smartDate($this->_date);
		}
		return Gen_Date::format($this->_date, $format, $this->_timezone, $this->_lang);
	}
	
	public function greaterThan($compare)
	{
		$compare = Gen_Date::create($compare->format('Y/m/d H:i'));
		return ($this->_date >= $compare);
	}
	
	public function __toString()
	{
		return (string) $this->format();
	}
}