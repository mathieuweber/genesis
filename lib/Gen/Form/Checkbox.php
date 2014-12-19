<?php
/** @see Gen_Form_Input */
require_once('Gen/Form/Input.php');

/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Form_Checkbox extends Gen_Form_Input
{
    protected $_type = 'checkbox';
    
    // protected $_displayLabel = 1;
    
    protected $_checkedValue = 1;
    
    protected $_uncheckedValue = 0;
    
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        parent::setValue($this->_checkedValue);
    }
    
    public function setCheckedValue($checkedValue)
    {
        $this->_checkedValue = $checkedValue;
        parent::setValue($checkedValue);
        return $this;
    }
    
    public function setValue($value)
    {
        if ($value == $this->_checkedValue) {
            $this->check();
        } else {
            $this->uncheck();
        }
        return $this;
    }
    
    public function getValue()
    {
        return ($this->isChecked() ? $this->_checkedValue : $this->_uncheckedValue);
    }
    
    public function isChecked()
    {
        return ($this->getAttribute('checked') ? true : false);
    }
    
    public function check()
    {
        $this->setAttribute('checked' , true);
        return $this;
    }
    
    public function uncheck()
    {
        $this->resetAttribute('checked');
        return $this;
    }
    
    // public function renderLabel()
    // {
        // return $this->_label ? '<span class="label_' . $this->_type . '">' . ($this->_label && $this->_enableTranslation ? _t($this->_enableTranslationHtml ? $this->_label : _sanitize($this->_label)) : $this->_label) . "</span>\n" : '';
    // }
}
