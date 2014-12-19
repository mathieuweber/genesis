<?php
require_once('Gen/Enum.php');

class Gen_Translate_Pattern extends Gen_Enum
{
	const VIEW  	 = 1;
	const CONTROLLER = 2;
	const FORM	  	 = 3;
	const MODEL   	 = 4;
	const JAVASCRIPT = 5;
	const FLASH	 	 = 6;
	const CONTEXT 	 = 7;
	const PLURAL 	 = 8;
	const DATABASE = 9;
	
	protected static $_data = array(
		self::VIEW  		=> array('key' => 'view', 'label' => "Vues", 'pattern' => '#_{1,2}t\("([^"\\\]*(?:\\\.[^"\\\]*)*)"#'),
		self::CONTROLLER  	=> array('key' => 'controller', 'label' => "Controllers", 'pattern' => '#(?:(?:\$this->setMessage)|(?:->addError)|(?:_{1,2}t))\("([^"\\\]*(?:\\\.[^"\\\]*)*)"#'),
		self::FORM  		=> array('key' => 'form', 'label' => "Formulaires", 'pattern' => '#(?:(?:->addError)|(?:->set(?:Label|Comment))|(?:_{1,2}t))\("([^"\\\]*(?:\\\.[^"\\\]*)*)"#'),
		self::MODEL  		=> array('key' => 'model', 'label' => "Constantes de classes", 'pattern' => '#"([^"\\\]*(?:\\\.[^"\\\]*)*)"#', 'condition' => 'extends Gen_Enum'),
		self::JAVASCRIPT  	=> array('key' => 'script', 'label' => "Javascript", 'pattern' => '#_t\("([^"\\\]*(?:\\\.[^"\\\]*)*)"#', 'extension' => 'js'),
		self::FLASH	  		=> array('key' => 'flash', 'label' => "Flash", 'pattern' => '#key="([^"\\\]*(?:\\\.[^"\\\]*)*)"#', 'extension' => 'xml'),
		self::CONTEXT   	=> array('key' => 'context', 'label' => "Context", 'pattern' => '#_{1,2}ct\("([^"\\\]*(?:\\\.[^"\\\]*)*)"(?:[\s]*,[\s]*\'([a-zA-Z0-9-]+)\')#'),
		self::PLURAL		=> array('key' => 'plural', 'label' => "Pluriel", 'pattern' => '#_{1,2}pt\("([^"\\\]*(?:\\\.[^"\\\]*)*)"(?:[\s]*,[\s]*"([^"\\\]*(?:\\\.[^"\\\]*)*)")#'),
		self::DATABASE  	=> array('key' => 'database', 'label' => "Base de données")
	);
	
	public static function getPatternById($id)
	{
		return static::getPropertyById($id, 'pattern');
	}
	
	public static function getExtensionById($id)
	{
		$extension = static::getPropertyById($id, 'extension');
		return $extension ? $extension : 'php';
	}
	
	public static function getConditionById($id)
	{
		return static::getPropertyById($id, 'condition');
	}
}
