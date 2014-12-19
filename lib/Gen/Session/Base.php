<?php
/** @see Gen_Hash */
require_once('Gen/Hash.php');

/**
 * @category   Gen
 * @package	Gen_Session
 */
class Gen_Session
{
		/**
	 * Constructor
	 *
	 * Instantiate using {@link getInstance()}; event handler is a singleton
	 * object.
	 *
	 * @return void
	 */
	protected function __construct()
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
			require_once('Gen/Hash.php');
			self::$_instance = new Gen_Hash($_SESSION);
		}

		return self::$_instance;
	}
	
	public static function start()
	{
		session_start();
	}
}