<?php
require_once('Gen/Form/List.php');
require_once('Gen/Form/Checkbox.php');

class Gen_Form_MultiCheckbox extends Gen_Form_List
{
	protected $_selectedValues = array();
	
	protected $_valueAttribute = 'id';
	
	protected $_labelAttribute = 'label';
	
	public function __construct()
	{
		parent::__construct('ul', array('class' => 'multicheckbox'));
	}
	
	public function setValue($value)
	{
		if($value instanceof Gen_Entity_Dictionary) {
			$value = $value->reduce($this->_valueAttribute);
		}
		$this->_selectedValues = (array) $value;
		return $this;
	}
	
	public function getValue()
	{
		return $this->_selectedValues;
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
	
	public function select($value)
	{
		array_push($this->_selectedValues, $value);
	}
	
	public function bind()
	{
		$name = $this->getName() . '[]';
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
			$checkbox = new Gen_Form_Checkbox();
			$checkbox->setCheckedValue($value)
					 ->setName($name)
					 ->disableWrapper();
			$span = new Gen_Html_Element('span');
			$span->append($label);
			foreach ($this->_selectedValues as $selectedValue) {
				if ($value == $selectedValue) {
					$checkbox->check();
				}
			}
			$li = new Gen_Html_Element('li');
			$li->append($checkbox)
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