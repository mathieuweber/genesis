<?php
require_once('Gen/View/Helper.php');
require_once('Gen/Repository.php');

/**
 * @category   Gen
 * @package	Gen_View
 */
class Gen_View_Base
{
	/**
	 * the base paths for view scripts
	 * @var string
	 */
	public static $defaultBaseDir = './app/view/';
	
	protected $_baseDir;
	
	/**
	 * the Path where layouts can be found
	 * @var string
	 */
	public static $layoutPath = 'layout';
	
	/**
	 * Data provided to the template
	 * @var array
	 */
	protected $_data = array(); 
	
	protected $_layout;
	
	/**
	 * Options = array('action', 'controller', 'module', 'format');
	 * @var array
	 */
	protected $_options;
	
	
	/**
	 * Block is used to redifine layout values inside a view
	 * @var string
	 */
	protected $_block;
	
	/**
	 * Indicates wether the View is rendered or not
	 * @var bool
	 */
	protected $_isRendered = false;
	
	public function getBaseDir()
	{
		if(null === $this->_baseDir) {
			$this->_baseDir = self::$defaultBaseDir;
		}
		return $this->_baseDir;
	}
	
	public function setBaseDir($baseDir)
	{
		$this->_baseDir = $baseDir;
		return $this;
	}
	
	public function assign($key, $value)
	{
		$this->_data[(string) $key] = $value;
	}
	
	public function getParam($key, $default = null)
	{
		return isset($this->_data[$key]) ? $this->_data[$key] : $default;
	}
	
	public function getData()
	{
		return $this->_data;
	}
	
	public function setLayout($layout)
	{
		$this->_layout = $layout;
		return $this;
	}
	
	public function getLayout()
	{
		return $this->_layout;
	}
	
	public function resetLayout()
	{
		$layout = $this->_layout;
		$this->_layout = null;
		return $layout;
	}
	
	public function getOption($key)
	{
		return isset($this->_options[$key]) ? $this->_options[$key] : null;
	}
	
	public function getOptions()
	{
		return isset($this->_options) ? $this->_options : array();
	}
	
	public function setOptions(array $options)
	{
		$this->_options = $this->_checkOptions($options);
		return $this;
	}
		
	public function buildOptions($options)
	{
		if (!is_array($options)) {
			if(preg_match('#::#', $options)) {
				$parts = preg_split('#::#', $options);
				$action = array_pop($parts);
				$options = array(
					'controller' => implode('::', $parts),
					'action' => $action
				);
			} else {
				$action = (string) $options;
				$options = $this->getOptions();
				$options['action'] = $action;
			}
		}
		$this->_checkOptions($options);
		return $options;
	}
	
	protected function _checkOptions(array $options)
	{
		if (!isset($options['controller']) || !isset($options['action'])) {
			require_once ('Gen/View/Exception.php');
			throw new Gen_View_Exception("Missing Argument 'controller' or 'action' " . print_r($options, true) . ' in ' . __CLASS__);
		}
		return $options;
	}
	
	public function setRendered($isRendered)
	{
		$this->_isRendered = (bool) $isRendered;
		return $this;
	}
	
	public function isRendered()
	{
		return $this->_isRendered;
	}
	
	public function setHeadMeta($key, $content, $equiv = null, $property = null, $charset = null, $URL = null)
	{
		Gen_Repository::getInstance('Gen_View_Head_Metas')->set($key, array(
			'name'=> $key,
			'content'=> $content,
			'http-equiv' => $equiv,
			'charset' => $charset,
			'URL' => $URL,
			'property' => $property
		));
		return $this;
	}
	
	public function getHeadMetas()
	{
		return Gen_Repository::getInstance('Gen_View_Head_Metas')->toArray();
	}
	
	public function setHeadLink($rel, $type, $href, $title = null)
	{
		$links = $this->getHeadLinks();
		$links[$rel] = array(
			'rel' => $rel,
			'type' => $type,
			'href' => $href,
			'title' => $title
		);
		Gen_Repository::getInstance()->set('Gen_View_Head_Links', $links);
		return $this;
	}
	
	public function setRss($url, $title = null)
	{
		return $this->setHeadLink('alternate', 'application/rss+xml', $url, $title);
	}
	
	public function getHeadLinks()
	{
		return Gen_Repository::getInstance()->get('Gen_View_Head_Links', array());
	}
	
	public function block($name)
	{
		if(null !== $this->_block) {
			require_once ('Gen/View/Exception.php');
			throw new Gen_View_Exception('Can not call nested blocks in' . __CLASS__);
		}
		$this->_block = (string) $name;
		ob_start();
	}
	
	public function endBlock()
	{
		if (null === $this->_block) {
			require_once ('Gen/View/Exception.php');
			throw new Gen_View_Exception('Can not call endBlock without calling block first in' . __CLASS__);
		}
		$this->setContentFor($this->_block, ob_get_clean());
		$this->_block = null;
	}
	
	public function getContent($name = 'Gen_View_Default_Block', $default = null)
	{
		return Gen_Repository::getInstance('Gen_View_Block')->get($name, $default);
	}
	
	public function setContentFor($key, $value)
	{
		Gen_Repository::getInstance('Gen_View_Block')->set($key, $value);
	}
	
	public function setInternalScript($script)
	{
		Gen_Repository::getInstance('Gen_View_Internal_Scripts')->add($script);
		return $this;
	}
	
	public function getInternalScripts()
	{
		return array_unique(Gen_Repository::getInstance('Gen_View_Internal_Scripts')->toArray());
	}
	
	public function setExternalScript($script)
	{
		Gen_Repository::getInstance('Gen_View_External_Scripts')->add($script);
		return $this;
	}
	
	public function getExternalScripts()
	{
		return array_unique(Gen_Repository::getInstance('Gen_View_External_Scripts')->toArray());
	}
	
	public function partial($options, array $params = array())
	{
		$options = $this->buildOptions($options);
		$file = $this->_formatFileName($options);
		$params = array_merge($this->_data, $params);
		return $this->renderFile($file, $params);
	}
	
	public function render(array $options, $layout = null)
	{
		$this->setOptions($options);
		if ($layout) $this->setLayout($layout);
		$file = $this->_formatFileName($options);
		return $this->loop($file);
	}
	
	public function loop($file) {
		$content = $this->renderFile($file, $this->_data);
		if ($layout = $this->resetLayout()) {
			$options = $this->buildOptions($layout);
			$this->setContentFor('Gen_View_Default_Block', $content);
			return $this->loop($this->_formatFileName($options));
		}
		return $content;
	}
	
	// protected function _formatLayoutFileName($layout)
	// {
		// return $this->getBaseDir() . self::$layoutPath . '/' . $layout . '.php';
	// }
	
	protected function _formatFileName($options)
	{
		$module = isset($options['module']) ? $options['module'] . '/' : null;
		$format = (isset($options['format']) && $options['format']) ? $options['format'] . '/' : null;
		$controller = str_replace('::', '/', $options['controller']);
		Gen_Log::log($options, 'Gen_View::_formatFileName');
		return $this->getBaseDir() . $module . $controller . '/' . $format . $options['action'] . '.php';
	}
	
	public function renderFile($_file, array $_params = array())
	{
		if (file_exists($_file)) {
			try {
				Gen_Log::log($_file, 'Gen_View_Base::renderFile');
				ob_start();
				
				/** Enable to access the data directly using $ instead of $this-> in the template */
				extract($_params);
				
				/** Includes the template file */
				include ($_file);
				
				$_str = ob_get_clean();
				
				return $_str;
			} catch (Exception $e) {
				ob_end_clean();
				throw $e;
			}
		}
		throw new Exception("Unknown file: $_file in " . __CLASS__);
	}
}