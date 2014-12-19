<?php
/** @see Gen_Html_Element */
require_once('Gen/Html/Element.php');
require_once('Gen/Html/Script.php');
require_once('Gen/Validate.php');

/**
 * @category   Gen
 * @package    Gen_Form
 */
abstract class Gen_Form_Element extends Gen_Html_Element
{
    const DISPLAY_BEFORE = 0;
	
    const DISPLAY_AFTER = 1;
    
    protected $_wrapper = 'div';

    protected $_label;
    
    protected $_displayLabel = 0;
    
    protected $_comment;
    
    protected $_scripts = array();
    
    protected $_required = false;
    
    protected $_validator;
	
	protected $_enabled = true;
	
	protected $_enableScript = true;
	
	protected $_enableTranslation = true;
	
	protected $_enableTranslationHtml = true;

	protected $_unit;
	
	protected $_icon;
    
    public function setName($name)
    {
        return $this->setAttribute('name', (string) $name);
    }
    
    public function getName()
    {
        return $this->getAttribute('name');
    }
    
    public function setValue($value)
    {
    	return $this->setAttribute('value', trim((string) $value));
    }
    
    public function getValue()
    {
    	return $this->getAttribute('value');
    }
    
    public function setWrapper($wrapper)
    {
        $this->_wrapper = $wrapper;
		return $this;
    }
    
	public function setReadOnly($value = 1)
    {
    	$value = $value ? 1 : 0;
    	if ($value) {
    		$this->addClass('readonly');
    	} else {
    		$this->removeClass('readonly');
    	}
    	return $this->setAttribute('readonly', $value);
    }
    
	public function getReadOnly()
    {
    	return $this->getAttribute('readonly');
    }
    
    public function disableWrapper()
    {
        $this->_wrapper = null;
		return $this;
    }
    
    public function setLabel($label)
    {
        $this->_label = (string) $label;
        return $this;
    }
    
    public function getLabel()
    {
        return $this->_label;
    }
    
    public function setComment($comment)
    {
        $this->_comment = (string) $comment;
        return $this;
    }
    
    public function getComment($comment)
    {
        return $this->_comment;
    }
    
    public function appendScript($script)
    {
        $scriptNode = new Gen_Html_Script();
        $this->_scripts[] = $scriptNode->append($script);
        return $this;
    }
    
    public function includeScript($src)
    {
        $scriptNode = new Gen_Html_Script(array('src' => $src));
        $this->_scripts[] = $scriptNode;
        return $this;
    }
    
	public function disable()
	{
		$this->_enabled = false;
		return $this;
	}
	
	public function enable()
	{
		$this->_enabled = true;
		return $this;
	}
	
	public function enabled()
	{
		return $this->_enabled;
	}
	
	public function disableScript()
	{
		$this->_enableScript = false;
		return $this;
	}
	
	public function enableTranslation()
	{
		$this->_enableTranslation = true;
		return $this;
	}
	
	public function disableTranslation()
	{
		$this->_enableTranslation = false;
		return $this;
	}
	
	public function enableTranslationHtml()
	{
		$this->_enableTranslationHtml = true;
		return $this;
	}
	
	public function disableTranslationHtml()
	{
		$this->_enableTranslationHtml = false;
		return $this;
	}
	
	public function setHint($value)
	{
		$this->setAttribute('placeHolder', $value);
		return $this;
	}

	public function getHint()
	{
		return $this->getAttribute('placeHolder');
	}
	
	public function setIcon($value)
	{
		$this->_icon = $value;
		return $this;
	}

	public function getIcon()
	{
		return $this->_icon;
	}
	
	public function setUnit($unit)
	{
		$this->_unit = $unit;
		return $this;
	}

    /********************************************
     *              Item Renderers              *
     ********************************************/
    public function renderRequired()
    {
        return $this->_required ? '<span class="field-required">*</span>' : '';
    }
    
    public function renderLabel()
    {
        return $this->_label ? '<label id="' . $this->getId() . '_label" for="'.$this->getId().'">' . ($this->_enableTranslation ? _t($this->_enableTranslationHtml ? $this->_label : _sanitize($this->_label)) : $this->_label) . $this->renderRequired() ."</label>\n" : '';
    }
    
    public function renderComment()
    {
        return $this->_comment ? '<div class="comment">' . ($this->_enableTranslation ? _t($this->_enableTranslationHtml ? $this->_comment : _sanitize($this->_comment)) : $this->_comment) . "</div>\n" : '';
    }

	public function renderUnit()
	{
		return $this->_unit ? '<div id="' . $this->getId() . '_unit" class="unit">'.$this->_unit.'</div>': '';
	}
    
    public function render()
    {
        if (!$this->_enabled) {
			return null;
		}
		
		if(!$this->_wrapper) {
			return parent::render();
		}
		
		$str = '<div class="field" id="'.$this->getId().'_field">';	
		$str .= $this->renderLabel();
		$str .=	'<div class="field-content">';
		if($this->_icon) {
			$str .= '<div class="field-group">';
		}
		$str .=	parent::render();
		if($this->_icon) {
			$str .= '<span class="field-icon"><i class="fa fa-'.$this->_icon.'"></i></span></div>';
		}
		$str .= $this->renderUnit()
			  . $this->renderComment()
			  . $this->renderErrors()
			  . '</div>';
		$str .= $this->renderScript()
			 . implode("\n", $this->_scripts);
		$str .= '</div>';				   
        
		return $str;
    }
    
    /****************************
     *        Validator         *
     ****************************/
     
    public function getValidator()
    {
        if (!isset($this->_validator)) {
            $this->_validator = new Gen_Validate();
        }
        return $this->_validator;
    }
    
    public function isValid($value = null)
    {
        $this->setValue($value);
		$value = $this->getValue();
        if ($this->_required || !empty($value)) {
            return $this->getValidator()->isValid($value);
        }
        $this->getValidator()->resetErrors();
        return true;
    }
    
    public function validate($condition, $param = null)
    {
        $this->getValidator()->validate($condition, $param);
        return $this;
    }
    
    public function setRequired($required = true)
    {
        $this->_required = (bool) $required;
        if ($this->_required === true) {
			$this->setAttribute('required', 'required');
            $this->validate('presence');
        } else {
			$this->resetAttribute('required');
		}
        return $this;
    }
    
    public function isRequired()
    {
        return (null === $this->_required) ? $this->_required : false;
    }
	
	public function getErrors()
    {
        return $this->getValidator()->getErrors();
    }
    
    public function addError($message, $param = null)
    {
        $this->getValidator()->addError($message, $param);
        return $this;
    }
    
    public function renderErrors()
    {
        $str = null;
        foreach ($this->getErrors() as $error) {
            $str .= '<div class="validation-error">' . $error . '</div>';
        }
        return $str;
    }
    
    public function renderScript()
    {
    	$str = $this->_enableScript ? $this->requireScript() : null;
    	return $str ? '<script type="text/javascript">'.$str.'</script>' : null;
    }
    
    public function requireScript()
    {
    	return false;
    }
}
