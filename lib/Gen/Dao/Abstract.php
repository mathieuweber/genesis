<?php
abstract class Gen_Dao_Abstract
{	
	protected static $_logger;
	
	private static $_adapter;
	
	private static $_config;
	
	protected static $_prefix;
	
	protected $_name;
	
	public static function config($driver, $host, $dbname, $user, $password, $prefix = null)
	{
		self::$_config = array(
			'DRIVER' => $driver,
			'HOST' => $host,
			'DBNAME' => $dbname,
			'USER' => $user,
			'PASSWORD' => $password
		);
		self::$_prefix = $prefix;
		return true;
	}
	
	public static function getAdapter()
	{
		if (!isset(self::$_adapter)) {
			$dns = self::$_config['DRIVER']
				 . ":host=" . self::$_config['HOST']
				 . ";dbname=" . self::$_config['DBNAME'];
			
			self::$_adapter = new PDO($dns, self::$_config['USER'], self::$_config['PASSWORD'], array(
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		}
		return self::$_adapter;
	}
	
	public function getName()
	{
		return $this->_name;
	}
	
	public function getTableName()
	{
		return (self::$_prefix ? self::$_prefix . '_' : '') . $this->_name; 
	}
	
	public function findByFilters(array $filters = array())
	{
		$options = $this->buildOptions($filters);
		$bind = $options['bind'];
		unset($options['bind']);
		return $this->findAll($options, $bind);
	}
	
	public function findById($id, array $options = array(), array $bind = array())
	{	
		if (isset($options['lang'])) {
			$options['where'][] = $this->getTableName().'_lang.parent_id = :id';
		} else {
			$options['where'][] = $this->getTableName().'.id = :id';
		}
		
		$bind['id'] = $id; 
		$result = $this->findAll($options, $bind);
		return array_pop($result);
	}
	
	public function findByIds(array $ids = array(), array $filters = array())
	{
		return $this->findByProperty('id', $ids, $filters);
	}
	
	public function findByProperty($property, array $ids = array(), array $filters = array())
	{
		$options = $this->buildOptions($filters);
		$bind = $options['bind'];
		
		$sql = null;
		$i=0;
		$ids = array_unique($ids);
		
		foreach ($ids as $id)
		{
			if($id)
			{
				$i++;
				$key = 'key_'.$i;
				
				if (isset($options['lang'])) {
					$options['cols'] = $this->getTableName() .'.*, '. $this->getTableName() . '_lang' .'.*';
					$options['join'] = 'INNER JOIN '. $this->getTableName() . '_lang'
									 . ' ON '. $this->getTableName() . '_lang' .'.parent_id = '. $this->getTableName() .'.id'
									 . ' AND ' . $this->getTableName() . '_lang.lang = :lang' ;
					$bind['lang'] = $options['lang'];
				}
				$opt = $options;
				$opt['where'][] = '`'.$this->getTableName().'`.`'.$property.'` = :' . $key;
				$sql .= ($sql ? ' union ' : '') . self::buildFinder($this->getTableName(), $opt);
				$bind[$key] = $id;
			}
		}
		if(null !== $sql) {
			return $this->findBySql($sql, $bind);
		}
		return false;
	}

	public function findOne($options = array(), $bind = array())
	{
		$options['limit'] = 1;
		$result = $this->findAll($options, $bind);
		return array_pop($result);
	}
	
	public function findAll(array $options = array(), array $bind = array())
	{
		if (isset($options['lang'])) {
			$options['cols'][] = $this->getTableName() .'.*, '. $this->getTableName() . '_lang' .'.*';
			$options['join'][] = 'INNER JOIN '. $this->getTableName() . '_lang'
							 . ' ON '. $this->getTableName() . '_lang' .'.parent_id = '. $this->getTableName() .'.id'
							 . ' AND ' . $this->getTableName() . '_lang.lang = :lang' ;
			$bind['lang'] = $options['lang'];
		}
		$query = self::buildFinder($this->getTableName(), $options);
		return $this->findBySql($query, $bind);
	}
	
	public function findBySql($sql, array $bind = array())
	{
		$stmt = $this->query($sql, $bind);
		$result = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$result[$row['id']] = $row;
		}

		return $result;
	}
	
	public function count(array $filters = array())
	{
		$options = $this->buildOptions($filters);
		$options['cols'] = 'count(distinct('. $this->getTableName() .'.id)) as count';
		
		if (isset($options['lang'])) {
			$options['join'][] = 'INNER JOIN '. $this->getTableName() . '_lang'
							 . ' ON '. $this->getTableName() . '_lang' .'.parent_id = '. $this->getTableName() .'.id'
							 . ' AND ' . $this->getTableName() . '_lang.lang = :lang' ;
			$options['bind']['lang'] = $options['lang'];
		}
		$sql = $this->buildFinder($this->getTableName(), $options);
		$stmt = $this->query($sql, $options['bind']);
		
		$rows = $stmt->fetchAll();
		return $rows[0]['count'];
	}
	
	public function findIds(array $options = array(), array $bind = array())
	{
		$options['cols'] = '`'. $this->getTableName() . '`.id';
		$sql = self::buildFinder($this->getTableName(), $options);
		$stmt = $this->query($sql, $bind);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}
	
	public function insert(array $data, array $options = array())
	{
		$table = $this->getTableName() . (isset($options['lang']) ? '_lang' : '');
		$sql = self::buildInsert($table, $data);
		$this->query($sql, $data);
		return self::getAdapter()->lastInsertId();
	}
	
	public function updateById($id, array $data, array $options = array(), array $bind = array())
	{
		$options['where'][] = (isset($options['lang']) ? '`parent_id`' : '`id`') . ' = :id';
		$bind['id'] = $id;
		return $this->update($data, $options, $bind);
	}
	
	public function update(array $data, array $options = array(), array $bind = array())
	{
		$table = $this->getTableName();
		if(isset($options['lang'])) {
			$options['where'][] = '`lang` = :lang';
			$bind['lang'] = $options['lang'];
			$table .= '_lang';
		}
		$sql = self::buildUpdate($table, $data, $options);
		$bind = array_merge($data, $bind);

		$stmt = $this->query($sql, $bind);
		return true;
	}
	
	public function deleteById($id, array $options = array(), array $bind = array())
	{
		$options['where'][] = '`' . $this->getTableName() . '`.`id` = :id';
		$bind['id'] = (int) $id;
		$options['limit'] = 1;
		return $this->delete($options, $bind);
	}
	
	public function delete(array $options, array $bind = array())
	{
		$table = $this->getTableName();
		if(isset($options['lang'])) {
			$options['where'][] = '`lang` = :lang';
			$bind['lang'] = $options['lang'];
			$table .= '_lang';
		}
		$sql = self::buildDelete($table, $options); 
		$stmt = $this->query($sql, $bind);
		return true;
	}
	
	public function query($sql, array $bind = array())
	{
		$stmt = $this->getAdapter()->prepare($sql);
		
		if (false === $stmt->execute($bind)) {
			require_once('Gen/Dao/Exception.php');
			throw new Exception(
				"SQL error in Gen_Dao_Abstract::query()\n"
			  . "SQL: '$sql'\n"
			  . "Bindings: " . print_r($bind, true) . "\n"
			  . "Error: " . print_r($stmt->errorinfo(), true));
		}
		
		Gen_Log::log("<p>$sql</p><pre>" . print_r($bind, true) . '</pre>', get_class($this).'::query');
		return $stmt;
	}
	
	public function buildOptions(array $filters = array())
	{
		$options = $bind = array();
		
		if(isset($filters['deleted'])) {
			$options['where'][] = $this->getTableName() . '.deleted = :deleted';
			$bind['deleted'] = $filters['deleted'];
		}
		
		if(isset($filters['limit'])) {
			$options['limit'] = (int) $filters['limit'];
		}
		
		if(isset($filters['cols'])) {
			$options['cols'] = $filters['cols'];
		}
		
		if(isset($filters['order'])) {
			$options['order'] = $this->getTableName() . '.' . $filters['order'];
			if(isset($filters['sort'])) {
				$options['order'] .= ' ' . $filters['sort'];
			}
		}
		
		if(isset($filters['group'])) {
			$options['group'] = (int) $filters['group'];
		}
		
		if(isset($filters['rand'])) {
			$options['rand'] = (int) $filters['rand'];
		}
		
		if (isset($filters['npp'])) {
			$options['limit'] = (int) $filters['npp'];
			if(isset($filters['page'])) {
				$options['offset'] = (int) $filters['npp'] * (int) ($filters['page']-1);
			}
		}
		
		$options['bind'] = $bind;
		return $options;
	}
	
	public static function buildFinder($table, array $options = array())
	{
		$query = '';
		$options['table'] = isset($options['table']) ? $options['table'] : $table;
		
		if (isset($options['rand']) && is_int($options['rand'])) {
			$limit = isset($options['limit']) ? $options['limit'] : null;
			$options['limit'] = $options['rand'];
			if ($limit) $options['limit'] = max($limit, $options['rand']);
			$query .= 'SELECT * FROM (';
		}
		
		$query .= 'SELECT '
			   . (isset($options['cols']) ? implode(',', (array) $options['cols']) : '`' . $table . '`.*')
			   . ' from `' . $options['table'] . '`'
			   . self::buildJoin($options)
			   . self::buildWhere($options)
			   . self::buildGroup($options)
			   . self::buildOrder($options)
			   . self::buildLimit($options)
			   . self::buildOffset($options);
		
		if (isset($options['rand'])) {
			$options['order'] = 'rand()';
			if ($limit) {
				$options['limit'] = $limit;
			} else {
				unset($options['limit']);
			}
			
			$query .= ') rs';
			$query .= self::buildOrder($options)
				   . self::buildLimit($options);
		}
		
		return $query;
	}
	
	public static function buildInsert($table, array $data)
	{
		$cols = array();
		$markers = array();
		foreach ($data as $key => $value) {
			$cols[] = '`'. $key .'`';
			$markers[] = ':' . $key;
		}
		return 'INSERT `'. $table .'` ('. implode(',', $cols) . ') VALUES(' . implode(',', $markers) .')';
	}
	
	public static function buildUpdate($table, array $data, array $options = array())
	{		
		foreach ($data as $key => $value) {
			$fields[] = '`'. $key .'` = :'. $key;
		}
		if(!isset($fields)) {
			return false;
		}
		
		$query = 'UPDATE `'. $table .'`'
			   . self::buildJoin($options)
			   . ' SET ' . implode(',', $fields)
			   . self::buildWhere($options);
		return $query;
	}
	
	public static function buildDelete($table, array $options = array())
	{		
		$query = 'delete from `' . $table . '`'
			   . self::buildJoin($options)
			   . self::buildWhere($options)
			   . self::buildLimit($options);
		   
		return $query;
	}
	
	public static function buildWhere(array $options)
	{
		return (isset($options['where']) ? ' WHERE ' . implode(' AND ', (array) $options['where']) : null);
	}
	
	public static function buildJoin(array $options)
	{
		return (isset($options['join']) ? ' ' . implode(' ', (array) $options['join']) : null);
	}
	
	public static function buildGroup(array $options)
	{
		return (isset($options['group']) ? ' GROUP BY ' . implode(',', (array) $options['group']) : null);
	}
	
	public static function buildOrder(array $options)
	{
		return (isset($options['order']) ? ' ORDER BY ' . implode(',', (array) $options['order']) : null);
	}
	
	public static function buildLimit(array $options)
	{
		return (isset($options['limit']) ? ' LIMIT ' . (int) $options['limit'] : null);
	}
	
	public static function buildOffset(array $options)
	{
		return ((isset($options['offset']) && $options['offset'] >= 0) ? ' OFFSET ' . (int) $options['offset'] : null);
	}
}