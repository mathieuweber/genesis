<?php

class Gen_Enum
{
	protected static $_data = array();
	
	public static function get($id)
	{
		return isset(static::$_data[$id]) ? static::$_data[$id] : null;
	}
	
	public static function getPropertyById($id, $property, $default = null)
	{
		return (isset(static::$_data[$id]) && isset(static::$_data[$id][$property])) ? static::$_data[$id][$property] : $default; 
	}
	
	public static function getLabel($id)
	{
		return self::getPropertyById($id, 'label');
	}
	
	public static function getKey($id)
	{
		return self::getPropertyById($id, 'key');
	}
	
	public static function getDescription($id)
	{
		return self::getPropertyById($id, 'description');
	}
	
	public static function map($data)
	{
		return array_intersect_key(static::$_data, array_flip((array) $data));
	}
	
	public static function getProperties($property)
	{
		$properties = array();
		foreach (static::$_data as $id => $data) {
			if (isset($data[$property])) {
				$properties[$id] = $data[$property];
			}
		}
		return $properties;
	}
	
	public static function getKeys()
	{
		return self::getProperties('key');
	}
	
	public static function getLabels($translate = true)
	{
		return self::getProperties('label');
	}
	
	public static function toArray()
	{
		return static::$_data;
	}
}