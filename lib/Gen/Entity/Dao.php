<?php
/** @see Gen_Dao_Abstract */
require_once ('Gen/Dao/Abstract.php');
require_once ('Gen/Repository.php');

abstract class Gen_Entity_Dao extends Gen_Dao_Abstract
{        
    protected $_className;
	
	private function _checkType(Gen_Entity_Abstract $entity)
	{
		if ($entity instanceof $this->_className) {
			return true;
		}
		throw new Exception('Gen_Entity_Dao expects '. $this->_className .'. '. get_class($entity) . ' given.');
	}
	
	private function _getEntity()
	{
		if (isset($this->_className)) {
			return new $this->_className();
		}
		throw new Exception('Gen_Entity_Dao class name is not defined');
	}
	
	/**
     * Inserts a given Entity
     *
     * makes use of abstract function {@link _prepare()}
     *
     * @param  Gen_Entity_Abstract
     * @return bool
     */
    public function create(Gen_Entity_Abstract $entity, array $options = array())
    {
		$this->_checkType($entity);
		
		$lang = isset($options['lang']) ? $options['lang'] : null;
		
		unset($options['lang']);
		$data = $this->_prepare($entity);
		$this->insert($data);
        $entity->setId(self::getAdapter()->lastInsertId());
		
		if (null !== $lang) {
			$data = $this->_prepareML($entity);
			$data['parent_id'] = $entity->getId();
			$data['lang'] = $lang;
			
			$options['lang'] = $lang;
			$this->insert($data, $options);
		}
        
		return true;
    }
    
    /**
     * Updates a given Entity
     *
     * makes use of abstract function {@link _prepare()}
     *
     * @param  Gen_Entity_Abstract $entity
     * @return bool
     */
    public function modify(Gen_Entity_Abstract $entity, array $options = array(), array $bind = array())
    {
        $this->_checkType($entity);
		$id = $entity->getId();
		
		if (isset($options['lang'])) {
			$data = $this->_prepareML($entity);
			if ($this->findById($entity->getId(), $options)) {
				$this->updateById($id, $data, $options, $bind);
			} else {
				$data['parent_id'] = $entity->getId();
				$data['lang'] = $options['lang'];
				$this->insert($data, $options);
			}
		}
		
		unset($options['lang']);
		$data = $this->_prepare($entity);
        $this->updateById($id, $data, $options, $bind);
		
		return true;
    }
    
	
	/**
     * Returns a dictionary of entities from a SQL query
     *
     * @param  string $sql the query
     * @param  array $bind list of values to bind to the sql query
     * @return Gen_Entity_Dictionary
     */
    public function findBySql($sql, array $bind = array())
    {
		$stmt = $this->query($sql, $bind);
		require_once('Gen/Entity/Dictionary.php');

		$result = new Gen_Entity_Dictionary($this->_className);
		$rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$result->add($this->_cache($row));
			$rows[] = $row;
        }
		Gen_Log::log('<pre>'.print_r($rows, true).'</pre>', get_class($this).'::finBySql');
		return $result;
    }
	
	protected function _cache(array $row) {
		$namespace = 'Gen_Dao_Cache_' . $this->_className;
		if (!$entity = Gen_Repository::getInstance($namespace)->get($row['id'])) {
			$entity = $this->_getEntity();
			$this->_build($row, $entity);
			Gen_Repository::getInstance($namespace)->set($row['id'], $entity);
		}
		return $entity;
	}
	
    /**
     * Map an Entity to SQL Data
     *
     * @param Gen_Entity_Abstract
     * @return array
     */
    //protected function _prepare(Gen_Entity_Abstract $entity) { }

    /**
     * Creates an Entity from SQL Data
     *
     * @param array $data
	 * @param Gen_Entity_Abstract $entity
     */
    //protected function _build(array $row, Gen_Entity_Abstract $entity) { }
	
	/**
     * Retrieve an Entity by Id
     *
     * @param int $id
     * @param array $options
     * @return Gen_Entity_Abstract
     */
    public function findById($id, array $options = array(), array $bind = array())
    {	
        $options['where'][] = $this->getTableName() . '.id = :id';
		$bind['id'] = $id;
		$options['limit'] = 1;
        return $this->findAll($options, $bind)->first();
    }
	
	public function findByProperty($property, array $ids = array(), array $filters = array())
	{
		$result = parent::findByProperty($property, $ids, $filters);
		if(false === $result) {
			require_once('Gen/Entity/Dictionary.php');
			$result = new Gen_Entity_Dictionary($this->_className);
		}
		return $result;
	}
	
	public function getLastInsertId()
	{
		return $this->getAdapter()->lastInsertId();
	}
}