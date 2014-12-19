<?php
/** @see Gen_Dom_Element */
require_once('Gen/Dom/Element.php');

class Gen_Html_Script extends Gen_Dom_Element
{
	public function __construct(array $attributes = array())
	{
		$attributes = array_merge(array('type' => 'text/javascript'), $attributes);
		parent::__construct('script', $attributes);
		/** force content to ' ' */
		$this->append(' ');
	}
	
	public function setSrc($src)
	{
		$this->setAttribute('src', $src);
		return $this;
	}
	
	public function getSrc()
	{
		return $this->getAttribute('src');
	}
}