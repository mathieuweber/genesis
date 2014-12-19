<?php
/** @see Gen_Dom_Element */
require_once('Gen/Dom/Element.php');

class Gen_Html_Link extends Gen_Dom_Element
{
	public function __construct(array $attributes = array())
	{
		parent::__construct('link', $attributes);
	}
	
	public function setHref($href)
	{
		$this->setAttribute('href', $href);
		return $this;
	}
	
	public function getHref()
	{
		return $this->getAttribute('href');
	}
	
	public function setType($type)
	{
		$this->setAttribute('type', $type);
		return $this;
	}
	
	public function setRel($rel)
	{
		$this->setAttribute('rel',$rel);
		return $this;
	}
}