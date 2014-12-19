<?php
require_once('Gen/Form/List.php');
require_once('Gen/Form/Radio.php');

class Gen_Form_MultiRadio extends Gen_Form_List
{
	protected $_selectedValues = array();
	protected $_valueAttribute = 'id';
	protected $_labelAttribute = 'label';
	
	public function __construct()
	{
		parent::__construct('ul', array('class' => 'multiradio'));
	}
	
	public function setLabelAttribute($labelAttribute)
	{
		$this->_labelAttribute = (string) $labelAttribute;
		return $this;
	}
	
	public function setValueAttribute($valueAttribute) 
	{
		$this->_valueAttribute = (string) $valueAttribute;
		return $this;
	}
	
	public function setValue($value)
	{
		if ($value === null) $value = 0;
		$this->_selectedValues = (array) $value;
		return $this;
	}
	
	public function getValue()
	{
		return $this->_selectedValues;
	}
	
	public function select($value)
	{
		$this->_selectedValues = $value;
	}
	
	public function bind()
	{
		$name = $this->getName();
		foreach($this->_dataSource as $id => $entity) {
			if ($entity instanceof Gen_Entity_Abstract) {
				$value = $entity->readProperty($this->_valueAttribute);
				$label = $entity->readProperty($this->_labelAttribute);
			} elseif (is_array($entity)) {
				$value = $entity[$this->_valueAttribute];
				$label = $entity[$this->_labelAttribute];
			} else {
				$value = $id;
				$label = $entity;
			}
			
			$radio = new Gen_Form_Radio();
			$radio->setCheckedValue($value)
					 ->setName($name)
					 ->disableWrapper();
			$span = new Gen_Html_Element('span', array('class' => 'multiradio-label'));
			$span->append($label);
			foreach ($this->_selectedValues as $selectedValue) {
				if ($value == $selectedValue) {
					$radio->check();
				}
			}
			$li = new Gen_Html_Element('li', array('class' => 'multiradio-option'));
			$li->append($radio)
			   ->append($span);
			$this->append($li);
		}
	}
	
	public function render()
	{
		$this->bind();
		return parent::render();
	}
}