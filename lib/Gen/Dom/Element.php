<?php
/**
 * @category   Gen
 * @package	Gen_Dom
 */
class Gen_Dom_Element
{
	const INDENT = "\t";

	protected $_tag;
	
	protected $_attributes;
	
	protected $_content;
	
	protected $_childrens;
	
	public function __construct($tag, array $attributes = array(), $content = null)
	{
		$this->_tag		= (string) $tag;
		$this->setContent($content);
		$this->_attributes = $attributes;
		$this->init();
	}
	
	public function init()
	{
		/**
		 * for User implementation only
		 * please use inheritance and parent::__construct()
		 * if you need to add specific features
		 */
	}
	
	/********************************************
	 *				Attributes				*
	 ********************************************/
	public function setAttributes(array $attributes = array())
	{
		$this->_attributes = $attributes;
		return $this;
	}
	
	public function addAttributes(array $attributes = array())
	{
		$this->_attributes += $attributes;
		return $this;
	}
	
	public function getAttributes()
	{
		return $this->_attributes;
	}
	
	public function setAttribute($key, $value)
	{
		$this->_attributes[(string)$key] = (string) $value;
		return $this;
	}
	
	public function getAttribute($key, $default = null)
	{
		return (isset($this->_attributes[$key]) && ('' !== $this->_attributes[$key]))
			? $this->_attributes[$key]
			: $default;
	}
	
	public function resetAttribute($key)
	{
		$attr = $this->getAttribute($key);
		unset($this->_attributes[$key]);
		return $attr;
	}
	
	public function setId($id)
	{
		return $this->setAttribute('id', $id);
	}
	
	public function getId()
	{
		return $this->getAttribute('id');
	}
	
	/********************************************
	 *				 Content				  *
	 ********************************************/   
	public function getContent()
	{
		return $this->_content;
	}
	
	public function setContent($content)
	{
		$this->_content = (null !== $content) ? (array) $content : array();
		return $this;
	}
	
	public function append($node)
	{
		$this->_content[] = $node;
		return $this;
	}
	
	public function prepend($node)
	{
		array_unshift($this->_content, $node);
		return $this;
	}
	
	public function wrap($tag, array $attributes = array())
	{
		$element = new self($tag, $attributes);
		return $element->append($this);
	}
	
	public function addChild($tag, array $attributes = array(), $content = null)
	{
		$element = new self($tag, $attributes, $content);
		$this->append($element);
		return $element;
	}
	
	/********************************************
	 *			  Item Renderers			  *
	 ********************************************/
	
	/**
	 * Renders the attributes
	 * @use htmlspecialchars to sanitize the values
	 * @return string
	 */
	public function renderAttributes()
	{
		$str = '';
		foreach($this->_attributes as $name => $value) {
			$str .= ' ' . $name . '="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"';
		}
		return $str;
	}
	
	/**
	 * Renders the content
	 * @return string
	 */
	public function renderContent()
	{
		$str = implode("\n", $this->_content);
		return ($str !== null ? $str : '');
	}
	
	/**
	 * Renders the Dom Element
	 * @return string
	 */	 
	public function render()
	{
		$str = $this->renderStart()
			 . $this->renderContent()
			 . $this->renderEnd();
		return $str;
	}
	
	public function renderStart()
	{
		return '<' . $this->_tag . $this->renderAttributes() . '>';
	}
	
	public function renderEnd()
	{
		return '</' . $this->_tag . '>';
	}
	
	public function __toString()
	{
		try {
			$str = (string) $this->render();
			return $str;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
}