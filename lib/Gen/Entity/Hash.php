<?php
/** Gen_Hash_Base */
require_once ('Gen/Hash.php');

/**
 * @category   Gen
 * @package	Gen_Entity
 */
class Gen_Entity_Hash extends Gen_Hash
{
	public function set($key, Gen_Entity_Abstract $entity)
	{
		return parent::set($key, $entity);
	}
	
	public function merge($data)
	{
		if($data instanceof Gen_Hash_Base) {
			$data = $data->toArray();
		}
		$this->_data += $data;
		return $this;
	}
	
	public function get($mixed)
	{
		if ($mixed instanceof Gen_Entity_Abstract) {
			$mixed = $mixed->getId();
		}
		return parent::get($mixed);
	}
	
	public function add(Gen_Entity_Abstract $entity)
	{
		return parent::set($entity->getId(), $entity);
	}
	
	public function search(Gen_Entity_Abstract $entity)
	{
		return parent::search($entity);
	}
	
	public function extractClass($class)
	{
		$extract = clone $this;
		foreach ($this->_data as $key => $value) {
			if (!($value instanceof $class)) {
				$extract->reset($key);
			}
		}
		return $extract;
	}
	
	public function filterClass($class)
	{
		$filter = clone $this;
		foreach ($this->_data as $key => $value) {
			if ($value instanceof $class) {
				$filter->reset($key);
			}
		}
		return $filter;
	}
	
	public function where($property, $condition)
	{
		$filtered = clone $this;
		foreach($this->_data as $key => $value) {
			if ($value->readProperty($property) != $condition) {
				$filtered->reset($key);
			}
		}
		return $filtered;
	}
		
	public function join($separator, $pattern)
	{
	   require_once('Gen/Str.php');
	   return implode($separator, $this->map($pattern));
	}
	
	public function map($pattern)
	{
		$map = array();
		foreach ($this->_data as $key => $entity) {
			$map[$key] = $entity->map($pattern);
		}
		return $map;
	}
	
	public function unique($property)
	{
		$unique = new self();
		foreach ($this->_data as $entity){
			$unique->set($entity->$property, $entity);
		}
		return $unique;
	}
	
	public function reduce($property)
	{
		$reduced = array();
		foreach($this->_data as $key => $entity)
		{
			$reduced[$key] = $entity->readProperty($property);
		}
		return $reduced;
	}
	
	public function collect($properties)
	{
		$collected = new Gen_Entity_Hash();
		$properties = (array) $properties;
		foreach($this->_data as $entity) {
			foreach ($properties as $property) {
				$item = $entity->readProperty($property);
				$collected->set($item->getId(), $item);
			}
		}
		return $collected;
	}
}