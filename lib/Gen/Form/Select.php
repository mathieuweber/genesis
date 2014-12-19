<?php
require_once('Gen/Form/List.php');

class Gen_Form_Select extends Gen_Form_List
{
	protected $_selectedValue;
	protected $_valueAttribute = 'id';
	protected $_labelAttribute = 'label';
	protected $_emptyLabel = '-- select --';
	
	public function __construct(array $attributes = array())
	{
		parent::__construct('select', $attributes);
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
	
	public function setEmptyLabel($emptyLabel)
	{
		$this->_emptyLabel = $emptyLabel;
		return $this;
	}
	
	public function setValue($value)
	{
		$this->_selectedValue = ($value === '') ? null : $value;
		return $this;
	}
	
	public function getValue()
	{
		return $this->_selectedValue;
	}
	
	public function select($value)
	{
		return $this->setValue($value);
	}
	
	public function bind()
	{
		$option = new Gen_Html_Element('option');
		$option->setAttribute('value', '')
			   ->addClass('hint')
			   ->append($this->_emptyLabel);
		$this->append($option);
		
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
			
			$option = new Gen_Html_Element('option');
			$option->setAttribute('value', $value)
				   ->append($label);
			if ($value === $this->_selectedValue) {
				$option->setAttribute('selected', 'true');
			}
			$this->append($option);
		}
	}
	
	public function render()
	{
		$this->bind();
		return parent::render();
	}
}
