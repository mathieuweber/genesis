<?php
/** @see Gen_Form_Element */
require_once('Gen/Form/Element.php');

require_once('Gen/Form/Textbox.php');
require_once('Gen/Form/Hidden.php');

/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Form_Autocomplete extends Gen_Form_Element
{
	protected $_action;
	
	protected $_datasource = array();
	
	protected $_readOnly = 0;
	
	public function setAction($action)
	{
		$this->_action = $action;
		return $this;
	}
	
	public function __construct(array $attributes = array())
	{
		parent::__construct('div', $attributes);
		$this->addClass('autocomplete');
	}
	
	public function setDatasource(array $datasource)
	{
		$this->_datasource = $datasource;
		return $this;
	}
	
	public function requireScript()
	{
		$id = $this->getId();
		$value = isset($this->_datasource[$this->getValue()])
			   ? $this->_datasource[$this->getValue()]
			   : null;
		$valueId = $this->getValue();
		
		$options = array();
		if(isset($value)) {
			$options['value'] = array($valueId, $value);
		}
		if($this->getReadOnly()) {
			$options['readonly'] = 1;
		}
		
		$options = json_encode($options);

		return "new Manolosanctis.Autocomplete('" . $id . "', '" . $this->_action ."', " . $options .");";

	}
}
