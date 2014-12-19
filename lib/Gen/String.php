<?php
require_once('Gen/Str.php');

/**
 * @category   Gen
 * @package	Gen_String
 */
class Gen_String
{
	protected $_str;
	
	public function __toString()
	{
		return $this->_str;
	}
	
	public function __construct($str = '')
	{
		$this->_str = (string) $str;
	}
	
	public function __call($name, $args)
	{
		if(!method_exists('Gen_Str', $name)) {
			throw new Exception('Call to undefined function ' . $name . ' in Gen_String');
		}
		array_unshift($args, $this->_str);
		$this->_str = call_user_func_array(array('Gen_Str', $name), $args);
		return $this;
	}
	
	public function suffix($suffix)
	{
		$this->_str .= $suffix;
		return $this;
	}
	
	public function prefix($prefix)
	{
		$this->_str = $prefix . $this->_str;
		return $this;
	}
	
	public function replace($search, $replace)
	{
		$this->_str = str_replace($search, $replace, $this->_str);
		return $this;
	}
	
	public function lenght()
	{
		return strlen($this->_str);
	}
	
	public function endsWith($search)
	{
		return ($search == Gen_Str::substr($this->_str, - strlen($search)));
	}
	
	public function ltrim($search)
	{
		$this->_str = ltrim($this->_str, $search);
		return $this;
	}
	
	public function rtrim($search)
	{
		$this->_str = rtrim($this->_str, $search);
		return $this;
	}
}