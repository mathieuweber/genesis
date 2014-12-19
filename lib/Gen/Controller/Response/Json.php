<?php
/** @see Gen_Http_Response */
require_once('Gen/Controller/Response.php');

/**
 * @category   Gen
 * @package	Gen_Controller_Response
 */
class Gen_Controller_Response_Json extends Gen_Controller_Response
{
	protected $_properties = array();
	
	public function __construct()
	{
		$this->setContentType('application/json; charset=utf-8');
		return $this;
	}
	
	public function setProperty($key, $value)
	{
		$this->_properties[(string) $key] = $value;
		return $this;
	}
	
	public function getProperty($key, $default = null)
	{
		return isset($this->_properties[$key]) ? $this->_properties[$key] : $default;
	}
	
	public function setProperties(array $properties)
	{
		$this->_properties = $properties;
		return $this;
	}
	
	public function addProperties(array $properties)
	{
		$this->_properties += $properties;
		return $this;
	}
	
	public function getProperties()
	{
		return $this->_properties;
	}
	
	public function outputBody()
	{
		echo self::toJson($this->_properties);
	}
	
	public static function toJson($mixed)
	{
		$str = '';
		
		if (is_array($mixed)) {
			foreach ($mixed as $key => $value) {
				$str .= ($str ? ', ' : '') . '"' . $key .'" : ' . self::toJson($value);
			}
			$str = '{' . $str . '}';
		} else {
			$str = '"' . self::sanitize($mixed) . '"';
		}
		
		return $str;
	}
	
	public function sanitize($text)
	{
		$text = utf8_decode((string) $text);
		$text = str_replace('"', '\"', $text);
		return utf8_encode(preg_replace("#\s#", ' ', $text));
	}
}