<?php

class Gen_Validate
{
	const DEFAULT_ERROR_MESSAGE = "Champ non valide.";
	
	const REGEXP_TITLE = '[a-zA-Z0-9].*';
	
	protected $_conditions;
	
	protected $_errors;
	
	protected $_messages;

	public static $_defaults = array(
		'email'	  			=> "Format d'email non valide.",
		'emails'	 		=> "Format d'email non valide.",
		'url'				=> "Format d'url invalide.",
		'regexp'	 		=> "Format de texte invalide.",
		'presence'   		=> "Cette valeur est obligatoire.",
		'length'	 		=> "Ce texte doit comporter {number} caractères",
		'min_length' 		=> "Ce texte est trop court. Il doit comporter au minimum {number} caractères",
		'max_length' 		=> "Ce texte est trop long. Il doit comporter au maximum {number} caractères",
		'acceptance' 		=> "Vous devez accepter pour continuer.",
		'min_file_size'  	=> "Le fichier fournit n'a pas une taille suffisante.",
		'max_file_size'  	=> "Le fichier fournit est trop volumineux.",
		'min_image_width'  	=> "La largeur de l'image est trop petite.",
		'max_image_width'  	=> "La largeur de l'image est trop grande.",
		'min_image_height'  => "La hauteur de l'image est trop petite.",
		'image_width'  	 	=> "La largeur de l'image doit être de {number} px.",
		'image_height'   	=> "La hauteur de l'image doit être de {number} px.",
		'file_extension' 	=> "L'extension du fichier n'est pas valide.",
		'file_upload'		=> "Erreur lors du téléchargement du fichier.",
		'younger_than'   	=> "Vous devez avoir moins de {number} ans.",
		'older_than'	 	=> "Vous devez avoir plus de {number} ans.",
		'title'		  		=> "Format de titre non valide. Il doit commencer par une lettre ou un chiffre.",
		'number'	 		=> "Format de nombre non valide.",
		'ean'				=> "Cet ISBN-13 n'est pas valide.",
		'available_ean' 	=> "Cet ISBN est déjà utilisé.",
		'positive_number'	=> "Valeur non valide.",
		'min'				=> "Minimum requis de {number}",
		'max'				=> "Maximum accepté de {number}",
	);
	
	public function __construct()
	{
		$this->_conditions = array();
		$this->_errors	 = array();
		$this->_messages   = array();
	}
	
	/**
	 * Gets the validation errors
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return $this->_errors;
	}
	
	/** 
	 * Sets external validation errors
	 *
	 * @param  array $errors
	 * @return Gen_Validate
	 */
	public function setErrors(array $errors)
	{
		$this->_errors = $errors;
		return $this;
	}
	
	/**
	 * Resets the errors
	 * needed by @see isValid
	 */
	public function resetErrors()
	{
		unset($this->_errors);
		$this->_errors = array();
	}
	
	/**
	 * Adds an error
	 * build the message based on the template message and the data
	 * @param  string $template
	 * @param  array  $data
	 */
	public function addError($template, $param = null)
	{
		if(!is_array($param)){
			$this->_errors[] = _t($template, array('number' => (string) $param));
		} else {
			$this->_errors[] = _t($template);
		}
	}
	
	/**
	 * Verify that the data is valid
	 * uses previously set conditions
	 * 
	 * @param  mixed $data
	 * @return bool
	 */
	public function isValid($data)
	{
		$valid = true;
		$this->resetErrors();
		foreach ($this->_conditions as $key => $value) {
			if (false === $this->_execute($key, $data, $value)) {
				$valid = false;
			}
		}
		return $valid;
	}
	
	/**
	 * Adds a validation rule (condition)
	 * 
	 * @param  string $condition function key for validation
	 * @param  mixed  $param additionnal parameter to match
	 * @param  mixed  $message optional error message
	 * @return Gen_Validate
	 */
	public function validate($condition, $param = null, $message = null)
	{
		$this->_conditions[$condition] = $param;
		if (null !== $message) {
			$this->_messages[$condition] = (string) $message;
		}
		return $this;
	}
	
	protected function _execute($key, $data, $value = null)
	{
		require_once('Gen/Str.php');
		$method = Gen_Str::camelize($key);

		if(method_exists($this, $method))
		{
			if (false === $this->$method($data, $value))
			{
				$this->_generateError($key);
				return false;
			}
			return true;
		}
			/** @todo: use Validate Exception */
			throw new Exception('Undefined validation method: '. $method .' in '. __CLASS__ .'::execute()');
	}
	
	protected function _generateError($key)
	{
		$template = isset($this->_messages[$key]) ? $this->_messages[$key] :
					isset(self::$_defaults[$key]) ? self::$_defaults[$key] : self::DEFAULT_ERROR_MESSAGE;
		$this->addError($template, $this->_conditions[$key]);
	}
	
	/**
	 * Validation Rules			
	 *
	 * validation rules (or conditions) are
	 * static public functions, so that they can be either
	 *
	 *	-	called directly for a quick validation Gen_Validate::email($value)
	 *	
	 *	-	chained through the Validate object and called via @see _execute().
	 *		in that case, the Validate Object will also provide
	 *		an array of errors corresponding to the validation
	 *
	 */
	 
	public static function email($value)
	{
		return (bool) filter_var(trim($value), FILTER_VALIDATE_EMAIL);
	}
	
	/**
	 * Validates one or ; separated emails
	 *
	 * @param  string $value
	 * @return bool
	 */
	public static function emails($value)
	{
		$emails = explode(';', $value);
		$valid = true;
		foreach ($emails as $email) {
			if (false === filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
				return false;
			}
		}
		return true;
	}
	
	public static function url($value)
	{
		return (bool) preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $value);
	}
	
	public static function regexp($value, $regexp)
	{
		return (bool) preg_match('#'. $regexp .'#', $value);
	}
	
	public static function presence($value)
	{
		return (null != $value);
	}
	
	public static function length($value, $target)
	{
		return (mb_strlen($value) == $target);
	}
	
	public static function minLength($value, $min)
	{
		return (mb_strlen($value) >= $min);
	}
	
	public static function maxLength($value, $max)
	{
		return (mb_strlen($value) <= $max);
	}
	
	public static function acceptance($value)
	{
		return ('on' == $value);
	}
	
	public static function minFileSize(array $file, $min)
	{
		if (!isset($file['name']) || !$file['name']) return true;
		
		$fileSize = filesize($file['tmp_name']);
		return ($fileSize >= $min);
	}
	
	public static function maxFileSize(array $file, $max)
	{
		if (!isset($file['name']) || !$file['name']) return true;
		
		$fileSize = filesize($file['tmp_name']);
		return ($fileSize <= $max);
	}

	public static function minImageWidth(array $file, $min)
	{
		if (!isset($file['name']) || !$file['name']) return true;
		
		$imageInfo = getimagesize($file['tmp_name']);
		return $imageInfo[0]  >= $min;
	}
	
	public static function maxImageWidth(array $file, $max)
	{
		if (!isset($file['name']) || !$file['name']) return true;
		
		$imageInfo = getimagesize($file['tmp_name']);
		return ($imageInfo[0]  <= $max);
	}
	
	public static function minImageHeight(array $file, $min)
	{
		if (!isset($file['name']) || !$file['name']) return true;
		
		$imageInfo = getimagesize($file['tmp_name']);
		return ($imageInfo[1]  >= $min);
	}
	
	public static function maxImageHeight(array  $file, $max)
	{
		if (!isset($file['name']) || !$file['name']) return true;
		
		$imageInfo = getimagesize($file['tmp_name']);
		return ($imageInfo[1] <= $max);
	}

	public static function imageWidth(array $file, $value)
	{
		if (!isset($file['name']) || !$file['name']) return true;
		
		$imageInfo = getimagesize($file['tmp_name']);
		return ($imageInfo[0] == $value);
	}
	
	public static function imageHeight(array $file, $value)
	{
		if (!isset($file['name']) || !$file['name']) return true;
		
		$imageInfo = getimagesize($file['tmp_name']);
		return ($imageInfo[1] == $value);
	}

	
	public static function fileExtension(array $file, $validExtensions)
	{
		if (!isset($file['name']) || !$file['name']) return true;
		
		$validExtensions = (array) $validExtensions;
		$split = explode('.',$file['name']);
		$ext = strtolower(array_pop($split));
		return in_array($ext, $validExtensions);
	}
	
	public static function fileUpload(array $file)
	{
		if(!isset($file['error'])) {
			return false;
		}
		return ((0 == $file['error']) && is_uploaded_file($file['tmp_name']) && isset($file['name']) && $file['name']);
	}
	
	public function youngerThan($date, $age)
	{
		require_once('Gen/Date.php');
		return (Gen_Date::getAge($date) <= (int) $age);
	}
	
	public static function olderThan($date, $age)
	{
		require_once('Gen/Date.php');
		return (Gen_Date::getAge($date) >= (int) $age);
	}
	
	public static function title($title)
	{
		return self::regexp($title, self::REGEXP_TITLE);
	}
	
	public static function number($number)
	{
		return is_numeric($number);
	}

	public static function ean($number)
	{
		if(is_numeric($number) && strlen($number)==13)
		{
			$sum = 0;

			for($i=0;$i<6;$i++)
			{
				$sum += substr($number, 2*$i+1, 1) * 3;
				$sum += substr($number, 2*$i, 1);
			}
			if((10-$sum%10)%10==substr($number, 12, 1))
			{
				return true;
			}
		}
		return false;
	}

	public static function availableEan($ean)
	{
		require_once ('Bo/Album/Sku.php');
		return Bo_Album_Sku::availableEan($ean);
	}

	public static function positiveNumber($number)
	{
		return is_numeric($number) && $number>=0;
	}

	public static function min($number, $min)
	{
		return (int) $number >= (int) $min;
	}
	
	public static function max($number, $max)
	{
		return (int) $number <= (int) $max;
	}
}
