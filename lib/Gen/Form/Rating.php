<?php
require_once('Gen/Form/List.php');
require_once('Gen/Form/Checkbox.php');

class Gen_Form_Rating extends Gen_Form_List
{
	public function __construct()
	{
		parent::__construct('ul', array('class' => 'inline rating'));
	}
	
	public function bind()
	{
		$name = $this->resetAttribute('name');
		$selectedValue = $this->resetAttribute('value');
		foreach($this->_dataSource as $value => $label) {
			$radio = new Gen_Html_Element('input', array('type' => 'radio'));
			$radio->setAttribute('value', $value)
				  ->setAttribute('name', $name);
			if ($selectedValue == $value) {
				$radio->setAttribute('checked', true);
			}
			$li = new Gen_Html_Element('li');
			$li->append($radio);
			$this->append($li);
		}
	}
	
	public function render()
	{
		$this->bind();
		return parent::render();
	}
}