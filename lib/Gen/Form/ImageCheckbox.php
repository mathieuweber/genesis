<?php
/** @see Gen_Form_Input */
require_once('Gen/Form/Checkbox.php');

/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Form_ImageCheckbox extends Gen_Form_Checkbox
{
	protected $_titleLabel;
	protected $_labelClass;
	
	public function setTitleLabel($title){
		$this->_titleLabel = $title;
		
		return $this;
	}
	
	public function getTitleLabel(){
	 	return $this->_titleLabel;		
	}
	
	public function setLabelClass($class){
		$this->_labelClass = $class;
		
		return $this;
	}
	
	public function getLabelClass(){
	 	return $this->_labelClass;		
	}
	
	public function renderLabel()
	{
		if($this->_label)
			return '<label id="' . $this->getId() . '_label" for="'.$this->getId().'" class="label_' . $this->_type . ' ' .$this->_labelClass .'" title="'.$this->getTitleLabel().'">' . $this->_label . "</label>";
		else
			return '<label id="' . $this->getId() . '_label" for="'.$this->getId().'" class="label_' . $this->_type . ' ' .$this->_labelClass .'" title="'.$this->getTitleLabel().'"></label>';
	}
}