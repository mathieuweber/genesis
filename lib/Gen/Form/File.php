<?php
/** @see Gen_Form_Input */
require_once('Gen/Form/Input.php');

/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Form_File extends Gen_Form_Input
{
	protected $_type = 'file';
	
	protected $_file;

	protected $_max_size;

	public function __construct(array $attributes = array())
	{
		$this->_max_size = 5*1024*1024;
		parent::__construct($attributes);
	}
	
	public function fileSize($min, $max)
	{
		$this->_max_size = $max;

		$this
			->setAttribute('maxlength', (int) $max)
			->validate('min_file_size', (int) $min)
			->validate('max_file_size', (int) $max);
		return $this;
	}
	
	public function mimeTypes($mimeTypes)
	{
		$mimeTypes = (array) $mimeTypes;
		$this->setAttribute('accept', implode(',', $mimeTypes));
		$this->validate('file_extension', $mimeTypes);
		return $this;
	}
	
	public function setValue($value)
	{
		return $this->_file = $value;
	}
	
	public function getValue()
	{
		return $this->_file;
	}
	
	public function isUploaded()
	{
		return (isset($_FILES[$this->getName()]) && ($_FILES[$this->getName()]['name'] !== null));
	}
	
	public function isValid($value = null)
	{
		$this->getValidator()->resetErrors();
		/** check if file was sent */
		if (!isset($_FILES[$this->getName()]) || (isset($_FILES[$this->getName()]) && $_FILES[$this->getName()]['name'] == null)) {
			if ($this->_required) {
				$this->addError("Erreur lors du téléchargement du fichier.");
				return false;
			}
		}
		
		/** validate the upload */
		$file = $_FILES[$this->getName()];

		return parent::isValid($file);
	}
}
