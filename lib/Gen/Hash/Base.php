<?php
/**
 * @category   Gen
 * @package	Gen_Hash
 */
class Gen_Hash_Base implements Iterator, ArrayAccess
{
	protected $_data = array();
	
	public function __construct($data = null)
	{
		if($data instanceof Gen_Hash_Base) {
			$data = $data->toArray();
		}
		$this->_data = (array) $data;
	}
	
	/**
	 * Add a new data reference
	 * Gen_Hash_Base must be extended type hinting
	 *
	 * @param mixed $key
	 * @param mixed $value
	 * @return Gen_Hash_Base $hash;
	 */
	public function set($key, $value)
	{
		$this->_data[(string) $key] = $value;
		return $this;
	}
	
	/**
	 * Unset a new data reference
	 *
	 * @param mixed $key
	 * @return Gen_Hash_Base $hash
	 */
	public function reset($key)
	{
		$result = $this->get($key);
		unset($this->_data[$key]);
		return $result;
	}
	
	public function update($data = array())
	{
		$this->_data = $data;
	}
	
	/**
	 * Get a given data
	 * @param mixed $key
	 * @return mixed|null $value;
	 */
	public function get($key ,$default = null)
	{
		return isset($this->_data[$key]) ? $this->_data[$key] : $default;
	}
	
	/**
	 * Merge a given Hash to the current context
	 * @param Gen_Hash_Base $hash
	 * @return Gen_Hash_Base $hash;
	 */
	public function merge($data)
	{
		if($data instanceof Gen_Hash_Base) {
			$data = $data->toArray();
		}
		$this->_data = array_merge($this->_data, $data);
		return $this;
	}
	
	/**
	 * Search the hash for a given value
	 * @param mixed $value
	 * @return mixed $key
	 */
	public function search($value)
	{
		return array_search($value, $this->_data, true);
	}
	
	public function count()
	{
		return count($this->_data);
	}
	
	public function isEmpty()
	{
		return (count($this->_data) > 0) ? false : true;
	}
	
	public function first()
	{
		return reset($this->_data);
	}
	
	public function last()
	{
		return end($this->_data);
	}
	
	public function shift()
	{
		return array_shift($this->_data);
	}
	
	public function exists($key)
	{
		return isset($this->_data[$key]);
	}
	
	/**
	 * Get the Data as an array
	 * @return array $array;
	 */
	public function toArray()
	{
		return $this->_data;
	}
	
	public function keys()
	{
		return array_keys($this->_data);
	}
	
	public function join($separator = "\n")
	{
		return implode($separator, $this->_data);
	}
	
	/** __toString() */
	public function __toString()
	{
		return $this->join();
	}
	
	/** Implements ArrayAccess */
	public function offsetSet($key, $value)
	{
		$this->set($key, $value);
	}
	
	public function offsetExists($key)
	{
		return isset($this->_data[$key]);
	}
	
	public function offsetUnset($key)
	{
		$this->reset($key);
	}
	
	public function offsetGet($key)
	{
		return $this->get($key);
	}
	
	/**
	 * Implements Iterator
	 *
	 * rewind()
	 * key()
	 * current()
	 * next()
	 * valid()
	 */
	public function rewind()
	{
		reset($this->_data);
	}
	
	public function key()
	{
		return key($this->_data);
	}
	
	public function current()
	{
		return current($this->_data);
	}
	
	public function next()
	{
		return next($this->_data);
	}
	
	public function valid()
	{
		return ($this->current() !== false);
	}
	
	/**
	 * Tools
	 */
	public function slice($offset, $length)
	{
		$this->_data = array_slice($this->_data, $offset, $length);
		return $this;
	}
}