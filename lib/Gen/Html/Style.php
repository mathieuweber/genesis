<?php
/** @see Gen_Dom_Element */
require_once('Gen/Html/Link.php');

class Gen_Html_Style extends Gen_Html_Link
{
	public function __construct(array $attributes = array())
	{
		$properties = array('rel' => 'stylesheet', 'type' => 'text/css');
		$attributes = array_merge($properties, $attributes);
		parent::__construct($attributes);
	}
}