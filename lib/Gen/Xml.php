<?php

require_once('Gen/Dom/Element.php');

class Gen_Xml extends Gen_Dom_Element
{

	protected $_version = '1.0';
	
	protected $_encoding = 'utf-8';
	
	public function getVersion()
	{
		return $this->_version;
	}
	
	public function setVersion($version)
	{
		$this->_version = $version;
		return $this;
	}

	public function getEncoding()
	{
		return $this->_encoding;
	}
	
	public function setEncoding($encoding)
	{
		$this->_encoding = $encoding;
		return $this;
	}
	
	public function render()
	{
		return '<?xml version="'. $this->_version .'" encoding="'. $this->_encoding .'"?>' . parent::render();
	}
}