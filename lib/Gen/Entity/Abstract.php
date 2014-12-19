<?php

require_once('Gen/Entity/Date.php');

abstract class Gen_Entity_Abstract
{		
	protected $_id = null;
	
	protected static function _int($value)
	{
		return (null === $value) ? null : (int) $value;
	}

	protected static function _string($value)
	{
		return (null === $value) ? null : (string) $value;
	}

	protected static function _bool($value)
	{
		return (null === $value) ? null : (bool) $value;
	}
	
	protected static function _float($value)
	{
		return (null === $value) ? null : (float) $value;
	}
	
	protected static function _date($value)
	{
		return new Gen_Entity_Date($value);
	}
	
	protected static function _array($value)
	{
		return (array) $value;
	}
	
    public function setId($id)
    {
        $this->_id = empty($id) ? null : (int) $id;
        return $this;
    }
    
    public function getId()
    {
        return $this->_id;
    }
    
	public function exists()
	{
		return (null !== $this->_id);
	}
	
    public function __construct($data = array())
	{
        $this->update($data);
	}
	
	public function writeProperty($key, $value)
	{
		$method = $this->formatWriter($key);
		if(method_exists($this, $method))
		{
			$this->$method($value);
			return $this;
		}
		return false;
	}
	
	public function readProperty($key, $default = null)
	{
		$method = $this->formatReader($key);
		if(method_exists($this, $method))
		{
			return $this->$method();
		}
		return $default;
	}
	
    public function writerExists($key)
    {
        $method = $this->formatWriter($key);
        return method_exists($this, $method);
    }
    
    public function readerExists($key)
    {
        $method = $this->formatReader($key);
        return method_exists($this, $method);
    }
	
	public function propertyExists($key)
	{
		return $this->readerExists($key) && $this->writerExists($key);
	}
    
    public function update(array $data)
    {
		foreach ($data as $key => $value) {
			if (false === $this->writeProperty($key, $value)) {
				if (('_id' == substr($key, -3))
					&& (null !== $entity = $this->readProperty(substr($key, 0, -3)))
					&& ($entity instanceof Gen_Entity_Abstract)
				) {
					$entity->setId($value);
				}
			}
		}
		return $this;
    }
    
	public function increment($property, $increment = 1)
	{
		$value = $this->readProperty($property) + (int) $increment;
		$this->writeProperty($property, $value);
		return $this;
	}
	
	public function toArray()
    {
        require_once('Gen/Str.php');
		$result = array();
        foreach (get_class_methods($this) as $method) {
            if('get' == substr($method,0,3)) {
                $key = Gen_Str::underscore(substr($method,3));
				$value = $this->$method();
                if ($value instanceof Gen_Entity_Abstract) {
                    $value = $value->getId();
					$key .= '_id';
                } elseif (($value instanceof Gen_Entity_Date) || ($value instanceof DateTime)) {
                    $value = $value->format('Y-m-d H:i:s');
                } elseif ($value instanceof Gen_Entity_Dictionary) {
                    $value = $value->keys();
					$key .= '_ids';
                }
				$result[$key] = $value;
            }
        }
        return $result;        
    }
    
	public function __toString()
	{
		return get_class($this) . ' Entity';
	}
	
	public function map($pattern)
	{
		require_once('Gen/Str.php');
        return Gen_Str::map($this, $pattern);
	}
	
    public function formatMethod($method)
	{
		require_once('Gen/Str.php');
		return Gen_Str::camelize($method, false);
	}
	
	public function formatReader($method)
	{
		return 'get' . $this->formatMethod($method);
	}

	public function formatWriter($method)
	{
		return 'set' . $this->formatMethod($method);
	}
}