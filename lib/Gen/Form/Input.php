<?php
/** @see Gen_Form_Element */
require_once('Gen/Form/Element.php');

/**
 * @category   Gen
 * @package    Gen_Form
 */
abstract class Gen_Form_Input extends Gen_Form_Element
{
    protected $_tag = 'input';
    
    protected $_type;
    
    public function __construct(array $attributes = array())
    {
        parent::__construct('input', $attributes);
        $this->setAttribute('type', $this->_type);
        $this->setAttribute('class', 'input-' . $this->_type);
    }
    
    public function getType()
    {
        return $this->getAttribute('type');
    }
}