<?php
/** @see Gen_Form_Element */
require_once('Gen/Form/Element.php');

require_once('Gen/Form/Textbox.php');
require_once('Gen/Form/Hidden.php');

/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Form_Textboxlist extends Gen_Form_Element
{
	protected $_action;
	
	protected $_feed = array();
	
	protected $_selectedValues = array();
	
	public function setValue($value)
	{
		return $this->_selectedValues = (array) $value;
	}
	
	public function getValue()
	{
		return $this->_selectedValues;
	}
	
	public function setAction($action)
	{
		$this->_action = $action;
		return $this;
	}
	
	public function feed(array $data)
	{
		$this->_feed = $data;
	}
	
	public function __construct(array $attributes = array())
	{
		parent::__construct('div', $attributes);
		$this->addClass('textboxlist');
	}
	
	public function renderValue()
	{
		$init = array();
		
		foreach($this->_feed as $key => $value) {
			$init[] = '<li id="'.$key.'">' . htmlentities($value, ENT_COMPAT, 'UTF-8') . '</li>';
		}
		return '<ul class="textbox-init" style="display:none">' . implode("\n", $init) . '</ul>';
	}
	
	public function render()
	{
		$name = $this->resetAttribute('name');
		
		$this->append('<span class="stretcher">:)</span>')
			 ->append($this->renderValue())
			 ->appendScript(
			"new Manolosanctis.Textboxlist('{$this->getId()}', '$name', '{$this->_action}');"
		);
		return parent::render();
	}
}
