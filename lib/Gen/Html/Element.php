<?php
/** @see Gen_Html_Element */
require_once('Gen/Dom/Element.php');

/**
 * @category   Gen
 * @package	Gen_Form
 */
class Gen_Html_Element extends Gen_Dom_Element
{
	protected $_display;
	
	protected $_styles;
	
	protected $_script;
	
	public function __construct($tag, array $attributes = array())
	{
		$this->_display = true;
		$this->_styles = array();
		parent::__construct($tag, $attributes);
	}
	
	/**
	 * Determines wether the Element should be displayed or not
	 * 
	 * caution ! this will not hide the element
	 *   but literaly not generate any code
	 * @see render()
	 *
	 * @param  bool $display
	 * @return Gen_Html_Element
	 */
	public function setDisplay($display)
	{
		$this->_display = (bool) $display;
		return $this;
	}
	
	public function getDisplay()
	{
		return $this->_display;
	}
	
	/**
	 * Styles
	 */
	public function getStyles()
	{
		return $this->_styles;
	}
	
	public function setStyles(array $styles)
	{
		$this->_styles = $styles;
		return $this;
	}
	
	public function getStyle($key, $default = null)
	{
		return isset($this->_styles[$key]) ? $this->_styles[$key] : $default;
	}
	
	public function setStyle($key, $value) {
		$this->_styles[(string) $key] = (string) $value;
		return $this;
	}
	
	public function setWidth($width) {
		$this->_styles['width'] = $width;
		return $this;
	}

	public function setHeight($height) {
		$this->_styles['height'] = $height;
		return $this;
	}
	
	public function hide()
	{
		return $this->setStyle('display', 'none');
	}
	
	/**
	 * Class
	 */
	public function setClass($class)
	{
		return $this->setAttribute('class', (string) $class);
	}
	
	public function getClass()
	{
		return $this->getAttribute('class');
	}
	
	public function addClass($class)
	{
		$class = $this->getClass() . " " . (string) $class;
		$this->setClass($class);
		return $this;
	}
	
	public function removeClass($class)
	{
		$classStr = $this->getClass();
		preg_replace("#\b$class\b#", ' ', $classStr);
		$this->setClass(trim($classStr));
		return $this;
	}
	
	public function render()
	{
		if($this->_display) {
			if ($this->_styles) {
				$style = '';
				foreach($this->_styles as $key => $value) {
					$style .= ($style ? ' ': '') . "$key: $value;";
				}
				$this->setAttribute('style', $style);
			}
			return parent::render();
		}
		return '';
	}
}