<?php

/**
 * Event. which can be listened
 */
class Gen_Controller_Event
{
	protected $value = null;
	
	protected $processed = false;
	
	protected $name;
	
	protected $parameters;

	/**
	 * Constructs a new Event.
	 *
	 * @param string  $name		 The event name
	 * @param array   $parameters   An array of parameters
	 */
	public function __construct($name, $parameters = array())
	{
		$this->name = $name;
		$this->parameters = $parameters;
	}

	/**
	 * Returns the event name.
	 *
	 * @return string The event name
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets the return value for this event.
	 *
	 * @param mixed $value The return value
	 */
	public function setReturnValue($value)
	{
		$this->value = $value;
	}

	/**
	 * Returns the return value.
	 *
	 * @return mixed The return value
	 */
	public function getReturnValue()
	{
		return $this->value;
	}

	/**
	 * Sets the processed flag.
	 *
	 * @param Boolean $processed The processed flag value
	 */
	public function setProcessed($processed)
	{
		$this->processed = (boolean) $processed;
	}

	/**
	 * Returns whether the event has been processed by a listener or not.
	 *
	 * @return Boolean true if the event has been processed, false otherwise
	 */
	public function isProcessed()
	{
		return $this->processed;
	}

	/**
	 * Returns the event parameters.
	 *
	 * @return array The event parameters
	 */
	public function getParams()
	{
		return $this->parameters;
	}

	/**
	 * Returns true if the parameter exists.
	 *
	 * @param  string  $name  The parameter name
	 *
	 * @return Boolean true if the parameter exists, false otherwise
	 */
	public function hasParam($name)
	{
		return array_key_exists($name, $this->parameters);
	}

	/**
	 * Returns a parameter value.
	 *
	 * @param  string  $name  The parameter name
	 * @param  mixed  $default  The default returned value
	 *
	 * @return mixed  The parameter value
	 */
	public function getParam($key, $default = null)
	{
		return (isset($this->parameters[$key]) && ($this->parameters[$key] !== '')) ? $this->parameters[$key] : $default;
	}

	/**
	 * Sets a parameter.
	 *
	 * @param string  $name   The parameter name
	 * @param mixed   $value  The parameter value
	 */
	public function setParam($name, $value)
	{
		$this->parameters[$name] = $value;
	}
}

