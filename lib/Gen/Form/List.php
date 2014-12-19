<?php
require_once('Gen/Form/Element.php');

class Gen_Form_List extends Gen_Form_Element
{
	protected $_dataSource = array();
	
	public function setDataSource($data)
	{
		$this->_dataSource = $data;
		return $this;
	}
	
	public function getDataSource()
	{
		return $this->_dataSource;
	}
}