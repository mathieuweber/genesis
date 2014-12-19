<?php
require_once('Gen/Controller/Event.php');
require_once('Gen/ClassLoader.php');

class Gen_Controller_Event_Dispatcher
{
	public static $observerDir = './app/Controller/';
	
	protected $listeners = array();
	
	/**
	 * 
	 * @param array $listerner array(observer,[module],action)
	 * @param Gen_Controller_Event $event
	 */
	public function notify(array $listerner, Gen_Controller_Event $event)
	{
		$module = null;
		if(array_key_exists('module',$listerner)){
			$module = $listerner['module'];
		}
		$className = Gen_ClassLoader::loadClass($listerner['observer'], $module, 'Controller', self::$observerDir);
		
		$observer = new $className();
		
		Gen_Log::log('START', $className .'::' . $listerner['action'], 'info');
		
		return $observer->processEvent($listerner['action'],$event);
	}
	
	
	/**
	 * Connects an observer to a given event name.
	 *
	 * @param string  $name	  An event name
	 * @param mixed   $observer  A PHP callable
	 * @param array   $param	parameters needed by the listeners
	 */
	public function addListener($name, $observer,$paramNames=array())
	{
		if (!isset($this->listeners[$name])) {
			$this->listeners[$name] = array();
		}
		if(!is_array($observer)) {
			$observer = array('observer' => $observer, 'action' => $name);
		}
		$this->listeners[$name][] = $observer;
	}
	
	/**
	 * Connects a bunch of observers to a given event name.
	 *
	 * @param string  $name	  An event name
	 * @param array   $listener  A PHP callable
	 * @param array   $param	parameters needed by the listeners
	 */
	public function addListeners($name, $observers,$paramNames = array())
	{
		foreach ($observers as $observer){
			$this->addListener($name,$observer,$paramNames);
		}
	}

	/**
	 * Disconnects a listener for a given event name.
	 *
	 * @param string   $name	  An event name
	 * @param mixed	$listener  A PHP callable
	 *
	 * @return mixed false if listener does not exist, null otherwise
	 */
	public function removeListener($name, $observer)
	{
		if (!isset($this->listeners[$name])) {
			return false;
		}

		foreach ($this->listeners[$name] as $i => $callable) {
			if ($observer === $callable) {
				unset($this->listeners[$name][$i]);
			}
		}
	}

	/**
	 * Returns true if the given event name has some listeners.
	 *
	 * @param  string   $name	The event name
	 *
	 * @return Boolean true if some listeners are connected, false otherwise
	 */
	public function hasListeners($name)
	{
		if (!isset($this->listeners[$name])) {
			$this->listeners[$name] = array();
		}

		return (boolean) count($this->listeners[$name]);
	}

	/**
	 * Returns all listeners associated with a given event name.
	 *
	 * @param  string   $name	The event name
	 *
	 * @return array  An array of array listeners
	 */
	public function getListeners($name)
	{
		if (!isset($this->listeners[$name])) {
			return array();
		}

		return $this->listeners[$name];
	}
}