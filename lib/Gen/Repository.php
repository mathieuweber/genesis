<?php

class Gen_Repository
{
	/**
	  * the Singleton Instance
	  * @var Gen_Repository
	  */
	protected static $_instances;
	
	protected $_data;
	
	/**
	 * Constructor
	 *
	 * Gen_View_Stack implements singleton
	 * Instantiate using {@link getInstance()}
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$this->_data = array();
	}

	/**
	 * Enforce singleton; disallow cloning 
	 * 
	 * @return void
	 */
	private function __clone()
	{
	}

	/**
	 * Singleton instance
	 *
	 * @return Gen_Event_Handler
	 */
	public static function getInstance($namespace = 'Gen_Repository_Default')
	{
		if (!isset(self::$_instances[$namespace])) {
			self::$_instances[$namespace] = new self();
		}

		return self::$_instances[$namespace];
	}
	
	public function set($key, $value)
	{
		$this->_data[$key] = $value;
		return $this;
	}
	
	public function get($key, $default = null)
	{
		return isset($this->_data[$key]) ? $this->_data[$key] : $default;
	}
	
	public function add($value)
	{
		$this->_data[] = $value;
		return $this;
	}
	
	public function append($key, $value)
	{
		$this->_data[$key] = isset($this->_data[$key]) ? $this->_data[$key] + $value : $value;
		return $this;
	}
	
	public function prepend($key, $value)
	{
		$this->_data[$key] = isset($this->_data[$key]) ? $value + $this->_data[$key] : $value;
		return $this;
	}
	
	public function toArray()
	{
		return $this->_data;
	}
}