<?php
/**
 * @category   Gen
 * @package	Gen_Entity
 */
class Gen_Entity_Dictionary implements Iterator
{
	protected $_data;
	
	protected $_className;
	
	public function getClassName()
	{
		return $this->_className;
	}
	
	public function __toString()
	{
		return 'Gen_Entity_Dictionary '.$this->_className;
	}
	
	public function __construct($mixed)
	{
		$this->_data = array();
		if ($mixed instanceof Gen_Entity_Abstract) {
			$this->_className = get_class($mixed);
			$this->add($mixed);
			return $this;
		}
		
		if ($mixed instanceof Gen_Entity_Dictionary) {
			$this->_className = $mixed->getClassName();
			$this->update($mixed);
			return $this;
		}
		
		$this->_className = (string) $mixed;
	}
	
	/**
	 * Helpers
	 */
	private function _checkType($mixed)
	{
		if(($mixed instanceof Gen_Entity_Dictionary) && $mixed->typeOf($this->_className)) return true;
		if ($this->typeOf($mixed)) return true;
		
		throw new Exception ('Gen_Entity_Dictionary expects ' . $this->_className . '. ' . $mixed . ' given.');
	}
	
	public function typeOf ($mixed)
	{
		return is_object ($mixed) ?
			$mixed instanceof $this->_className
			: (is_subclass_of ($this->_className, $mixed)
			   || strtolower ($mixed) == strtolower ($this->_className));
	}
	
	/**
	 * Basic functions
	 */
	public function add(Gen_Entity_Abstract $entity)
	{
		$this->_checkType($entity);
		$this->_data[$entity->getId()] = $entity;
		return $this;
	}
	
	public function remove(Gen_Entity_Abstract $entity)
	{
		$this->_checkType($entity);
		$result = isset($this->_data[$entity->getId()]) ? $this->_data[$entity->getId()] : false;
		if($result) {
			unset($this->_data[$entity->getId()]);
		}
		return $result;
	}
	
	public function get($mixed, $default = null)
	{
		if ($mixed instanceof Gen_Entity_Abstract) {
			$mixed = $mixed->getId();
		}
		return isset($this->_data[$mixed]) ? $this->_data[$mixed] : $default;
	}
	
	public function update(Gen_Entity_Dictionary $data)
	{
		$this->_checkType($data);
		$this->_data = $data->toArray();
		return $this;
	}
	
	public function merge(Gen_Entity_Dictionary $data)
	{
		$this->_checkType($data);
		$this->_data += $data->toArray();
		return $this;
	}
	
	public function reset($mixed)
	{
		if ($mixed instanceof Gen_Entity_Dictionary || is_array($mixed)) {
			if ($mixed instanceof Gen_Entity_Dictionary) {
				$this->_checkType($mixed->getClassName());
			}
			
			$reseted = new self($this->_className);
			foreach ($mixed as $key => $value) {
				if ($entity = $this->reset($value)) {
					$reseted->add($entity);
				}
			}
			
			return $reseted;
		}
		
		$reseted = $this->get($mixed);
		if ($reseted) {
			unset($this->_data[$reseted->getId()]);
		}
		return $reseted;
	}
	
	public function reverse()
	{
		$this->_data = array_reverse($this->_data, true);
		return $this;
	}
	
	public function toArray()
	{
		return $this->_data;
	}
	
	/**
	 * Tools
	 */
	public function keys()
	{
		return array_keys($this->_data);
	}
	
	public function count()
	{
		return count($this->_data);
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
	 
	public function slice($offset, $length)
	{
		$this->_data = array_slice($this->_data, $offset, $length);
		return $this;
	}

	public function ksort($sort_flags = SORT_REGULAR)
	{
		ksort($this->_data, $sort_flags);
		return $this;
	}
	
	/** 
	 * Data processing
	 */
	 
	/**
	 * Filters the dictionary based on a property condition
	 * @param  string $property
	 * @param  mixed $condition
	 * @return Gen_Entity_Dictionary $filtered
	 */
	public function where($property, $conditions)
	{
		$conditions = (array) $conditions;
		$filtered = new self($this->_className);
		foreach ($this->_data as $key => $entity) {
			if(('_id' == substr($property, -3)) && (null !== $ref = $entity->readProperty(substr($property, 0, -3))) && ($ref instanceof Gen_Entity_Abstract)) {
				$value = $ref->getId();
			} else {
				$value = $entity->readProperty($property);
			}
			foreach($conditions as $condition) {
				if ($value == $condition) {
					$filtered->add($entity);
				}
			}
		}
		return $filtered;
	}
	
	public function except($property, $condition)
	{
		$filtered = new self($this->_className);
		foreach ($this->_data as $key => $entity) {
			if ($entity->readProperty($property) != $condition) {
				$filtered->add($entity);
			}
		}
		return $filtered;
	}
	
	/**
	 * Returns a dictionary with a unique property per entity
	 * @param  string $property
	 * @return Gen_Entity_Dictionary $unique
	 */
	public function unique($property)
	{
		$unique = array();
		foreach ($this->_data as $entity){
			$unique[$entity->readProperty($property)] = $entity;
		}
		return new self($this->_className, array_intersect($this->_data, $unique));
	}
	
	/**
	 * Returns a dictionary with a unique property per entity
	 * @param  string $property
	 * @return Gen_Entity_Dictionary $unique
	 */
	public function sum($property)
	{
		$sum = 0;
		foreach ($this->_data as $entity){
			$sum += $entity->readProperty($property);
		}
		return $sum;
	}
	
	/**
	 * Returns an array of all entities' $property
	 * @param  string $property
	 * @return Array $reduced
	 */
	public function reduce($property)
	{
		$reduced = array();
		foreach ($this->_data as $key => $entity) {
			$reduced[$key] = $entity->readProperty($property);
		}
		return $reduced;
	}
	
	/**
	 * Returns an array of entity properties
	 *
	 * This function is used to load all entities of a Dictionary at the same time
	 *
	 * @param  string properties to collect
	 * @return array reduced
	 */
	public function collect($mixed, $properties)
	{
		$collected = new Gen_Entity_Dictionary($mixed);
		$properties = (array) $properties;
		foreach ($this->_data as $key => $entity) {
			foreach ($properties as $property) {
				$item = $entity->readProperty($property);
				if ($item instanceof Gen_Entity_Abstract) {
					$collected->add($item);
				}
			}
		}
		return $collected;
	}
	
	public function groupBy($property)
	{
		$groups = array();
		foreach ($this->_data as $key => $entity) {
			$item = $entity->readProperty($property);
			if ($item instanceof Gen_Entity_Abstract) {
				$item = $item->getId();
			}
			
			if (!isset($groups[$item])) {
				$groups[$item] = new self($this->_className);
			}
			$groups[$item]->add($entity);
		}
		return $groups;
	}
	
	public function copy()
	{
		$copy = new self($this->_className);
		$copy->update($this);
		return $copy;
	}
	
	/**
	 * Implements Iterator
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

	public function toJson()
	{
		$json = $this->toArray();
		foreach ($json as $id => $jElement) {
			$json[$id] = $jElement->toArray();
		}
		
		$json = json_encode($json);
		return $json;
	}
}
