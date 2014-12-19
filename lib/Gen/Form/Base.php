<?php
require_once('Gen/Html/Element.php');
			
/** @see Gen_Html_Element */
require_once('Gen/Form/Element.php');
require_once('Gen/Form/Textbox.php');
require_once('Gen/Form/Textarea.php');
require_once('Gen/Form/File.php');
require_once('Gen/Form/Checkbox.php');
require_once('Gen/Form/ImageCheckbox.php');
require_once('Gen/Form/Hidden.php');			   
require_once('Gen/Form/Password.php');
require_once('Gen/Form/Radio.php');
require_once('Gen/Form/Submit.php');
require_once('Gen/Form/Select.php');
require_once('Gen/Form/MultiCheckbox.php');
require_once('Gen/Form/MultiRadio.php');
require_once('Gen/Form/Autocomplete.php');
require_once('Gen/Form/Textboxlist.php');
require_once('Gen/Form/Date.php');
require_once('Gen/Form/Time.php');
require_once('Gen/Form/Rating.php');
require_once('Gen/Form/Email.php');
require_once('Gen/Form/Url.php');
				
/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Form_Base extends Gen_Html_Element
{
	protected $_name = null;
	
	protected $_elements;
	
	public function __construct()
	{
		$this->_elements = array();
		parent::__construct('form', array('method' => 'post'));
		if ($this->_name) {
			$this->setId($this->_name . '_form');
		}
	}
	
	public function getElements()
	{
		return $this->_elements;
	}
	
	public function addElement(Gen_Form_Element $element)
	{
		$this->_elements[$element->getId()] = $element;
		$this->append($element);
	}
	
	public function getElement($id)
	{
		return isset($this->_elements[$id]) ? $this->_elements[$id] : null;
	}
	
	public function disable($id) 
	{
		$this->getElement($id)->disable();
		return $this;
	}
	
	public function setAction($action)
	{
		return $this->setAttribute('action', $action);
	}
	
	public function setMethod($method)
	{
		return $this->setAttribute('method', $method);
	}
	
	public function setName($name)
	{
		$this->_name = (string) $name;
		return $this;
	}
	
	public function getName()
	{
		return $this->_name;
	}
	
	public function getValues()
	{
		$values = array();
		foreach ($this->_elements as $key => $element) {
			if ($element->enabled() && !($element instanceof Gen_Form_Submit)&& !($element instanceof Gen_Form_File)) {
				$values[$key] = $element->getValue();
			}
		}
		return $values;
	}
	
	public function setValues($data)
	{
		if ($data instanceof Gen_Entity_Abstract) {
			$data = $data->toArray();
		}
		foreach ($this->_elements as $key => $element) {
			$value = isset($data[$key]) ? $data[$key] : null;
			$element->setValue($value);
		}
		return $this;
	}
	
	public function setValue($key, $value)
	{
		if ($element = $this->getElement($key)) {
			$element->setValue($value);
		}
		return $this;
	}
	
	public function getFiles()
	{
		$values = array();
		foreach ($this->_elements as $key => $element) {
			if ($element->enabled() && ($element instanceof Gen_Form_File)) {
				$values[$key] = $element->getValue();
			}
		}
		return $values;
	}
	
	public function moveFile($key, $fileName)
	{
		if(!isset($this->_elements[$key]) || !($this->_elements[$key] instanceof Gen_Form_File)) {
			return false;
		}
		$fileElmt = $this->_elements[$key];
		$file = $fileElmt->getValue();
		
		$dir = dirname($fileName);
		
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
		
		return move_uploaded_file($file['tmp_name'], $fileName);
	}
	
	public function buildId($key)
	{
		if ($name = $this->_name) {
			$key = $name .'_'. $key;
		}
		return $key;
	}
	
	public function buildName($key)
	{
		if ($name = $this->_name) {
			$key = $name .'['. $key .']';
		}
		return $key;
	}
	
	public function createElement($type, $id, array $attributes = array())
	{
		switch ($type) {
			case 'text':
			case 'textbox':
				$element = new Gen_Form_Textbox();
				break;
			
			case 'email':
				$element = new Gen_Form_Email();
				break;
			
			case 'url':
				$element = new Gen_Form_Url();
				break;
			
			case 'textarea':
				$element = new Gen_Form_Textarea();
				break;
				
			case 'file':
				$element = new Gen_Form_File();
				$element->setName($id);
				if(!$this->getAttribute('enctype')){$this->setAttribute('enctype','multipart/form-data');}
				break;
				
			case 'checkbox':
				$element = new Gen_Form_Checkbox();
				break;
			   
			case 'imagecheckbox':
				$element = new Gen_Form_ImageCheckbox();
				break;
				
			case 'hidden':
				$element = new Gen_Form_Hidden();
				break;
				
			case 'password':
				$element = new Gen_Form_Password();
				break;
			
			case 'radio':
				$element = new Gen_Form_Radio();
				break; 
			
			case 'select':
				$element = new Gen_Form_Select();
				break;	
			
			case 'multicheckbox':
				$element = new Gen_Form_MultiCheckbox();
				break;

			case 'multiradio':
				$element = new Gen_Form_MultiRadio();
				break;				 
							
			case 'autocomplete':
				$element = new Gen_Form_Autocomplete();
				break;
	
			case 'textboxlist':
				$element = new Gen_Form_Textboxlist();
				break; 
				
			case 'date':
			case 'datetime':
				$element = new Gen_Form_Date();
				if($type == 'datetime') {
					$element->enableTime();
				}
				break;
			
			case 'time':
				$element = new Gen_Form_Time();
				break;
				
			case 'rating':
				$element = new Gen_Form_Rating();
				break;
			
			case 'submit':
				$element = new Gen_Form_Submit();
				$element->setName('form[' . $id .']');
				break;
				
			default:
				require_once('Gen/Form/Exception.php');
				throw new Gen_Form_Exception("Unknown type: $type in Gen_Form::createElement");
				break;
		}
		
		if (($type != 'submit') && ($type != 'file')) {
			$element->setName($this->buildName($id));
		}
		$element->setId($id)
				->addAttributes($attributes);
		$this->addElement($element);
		
		return $element;
	}
	
	public function isValid($data = array())
	{
		$valid = true;
		
		if(!$data) {
			if(isset($_POST[$this->_name])) {
				$data = $_POST[$this->_name];
			}
		}

		foreach ($this->_elements as $key => $element) {
			$value = isset($data[$key]) ? $data[$key] : null;
			if ($element->enabled() && !$element->isValid($value)) {
				$valid = false;
			}
		}
		return $valid;
	}
	
	public function getErrors()
	{
		$errors = array();
		foreach ($this->_elements as $key => $element) {
			$errors = array_merge($errors, $element->getErrors());
		}
		return $errors;
	}
}
