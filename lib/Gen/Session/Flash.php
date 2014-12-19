<?php
class Gen_Session_Flash
{
	protected static $_instance;
	
	protected $_new;
	
	protected $_old;
	
	public function __construct()
	{
	
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
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getNew()
	{
		if (!isset($this->_new)) {
			require_once('Gen/Hash.php');
			$this->_new = new Gen_Hash();
		}
		return $this->_new;
	}
	
	public function getOld()
	{
		if (!isset($this->_old)) {
			require_once('Gen/Hash.php');
			$this->_old = new Gen_Hash();
		}
		return $this->_old;
	}
	
	public function get($key, $default = null) {
		return $this->getOld()->get($key, $default);
	}
	
	public function set($key, $value)
	{
		$this->getNew()->set($key, $value);
		return $this;
	}
	
	public function save()
	{
		if (isset($this->_new)) {
			$_SESSION['Gen_Session_Flash'] = $this->_new->toArray();
		}
		return $this;
	}
	
	public function load()
	{
		if (isset($_SESSION['Gen_Session_Flash'])) {
			$this->getOld()->update($_SESSION['Gen_Session_Flash']);
			unset($_SESSION['Gen_Session_Flash']);
		}
		return $this;
	}
}
