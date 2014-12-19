<?php
/**
 * @category   Gen
 * @package	Gen_Css
 */
class Gen_Css
{
	const LENGTH = '(([\+\-]?[0-9\.]+)(em|ex|px|in|cm|mm|pt|pc|\%))|0';
	
	const CSS3 = '(border-radius|box-shadow)';
	
	const CSS_BLOCK = '{([^}]+)}';
	
	const SELECTOR = '$';
	
	public static $properties = array('margin-left','margin-right','margin-top','margin-bottom','margin','padding-left','padding-right','padding-top','padding-bottom','padding','display', 'position', 'border-color', 'border-left','border-right', 'border-top', 'border-bottom', 'border-radius', 'border', 'color','background-color', 'font', 'font-size', 'font-weight', 'line-height', 'text-transform', 'text-decoration', 'list-style-type', 'width', 'min-width', 'max-width', 'height', 'min-height', 'max-height', 'top', 'bottom', 'right', 'left');
	
	public static $pseudoClasses = array('hover', 'before', 'after', 'focus', 'active');
	
	public static $cacheDir = 'public/styles/';
	
	public static $srcDir = 'css/';
	
	public static $debug = false;
	
	public static function getCacheFile($file)
	{
		return self::$cacheDir . $file;
	}
	
	public static function getSrcFile($file)
	{
		return self::$srcDir . $file;
	}
	
	public static function process($file)
	{
		$cacheFile = self::getCacheFile($file);
		$srcFile = self::getSrcFile($file);
		if (!file_exists($srcFile)) {
			return false;
		}
		if (!file_exists($cacheFile) || (filemtime($cacheFile) < filemtime($srcFile)) || self::$debug) {
			$content = self::parse($srcFile);
			file_put_contents($cacheFile, $content);
		}
		return true;
	}
	
	public static function parse($file)
	{
		if (!file_exists($file))
		{
			return false;
		}
		$str = file_get_contents($file);
		return self::parseStr($str);
	}
	
	public static function parseStr($str)
	{
		$str = self::importFilter($str);
		$str = self::constantFilter($str);
		$str = self::mixinFilter($str);
		$str = self::minify($str);
		return $str;
	}
	
	public static function match($pattern, $str, $flag = PREG_SET_ORDER)
	{
		$regexp = '/' . $pattern . '/s';
		if(preg_match_all($regexp, $str, $matches, $flag)) {
			return $matches;
		}
		return array();
	}
	
	public static function escape($str)
	{
		$str = str_replace('/', '\/', $str);
		$str = str_replace('.', '\.', $str);
		return $str;
	}
	
	public static function matchProperty($property, $str)
	{
		$regexp = '('. self::escape($property) .')\s*:\s*(.*?)\s*;';
		return self::match($regexp, $str);
	}
	
	public static function matchSelector($selector, $str)
	{
		$regexp = '('. self::escape($selector) .')\s*{\s*(.*?)\s*}';
		return self::match($regexp, $str);
	}
	
	/****************************
	 *			Filters			*
	 ****************************/	
	public static function importFilter($str)
	{
		$regexp = '#@import ?\("([^)]*)"\);#';
		preg_match_all($regexp, $str, $matches, PREG_SET_ORDER);
			foreach($matches as $match) {
				$replace = '/** ' . $match[0] . ' */';
				$file = self::getSrcFile($match[1]);
				if (file_exists($file)) {
					$replace .= "\n" . file_get_contents($file);
				}
				$str = str_replace($match[0], $replace, $str);
			}
		return $str;
	}
	
	public static function minify($str)
	{
		$str = preg_replace('#\/\*[\s\S]*?\*\/#', '', $str); // delete comments
		$str = preg_replace('#\s+#', ' ', $str); // Normalize white spaces
		$str = preg_replace('#\s*([!{}:;>+,\]\)])#', '$1', $str); // delete unnecessary white spaces
		$str = preg_replace('#([!{}:;>+\(\[,])\s*#', '$1', $str); // delete unnecessary white spaces
		$str = preg_replace('#}#', "}\n", $str); // split lines
		$str = preg_replace('#^\s+|\s+$#', '', $str); // trim file
		return $str;
	}
	
	public static function constantFilter($str)
	{
		$results = array();
		$regexp = '#@constants ?{([^}]+)}#s';
		preg_match($regexp, $str, $matches);
		if ($matches) {
			$parts = explode(';', rtrim(rtrim($matches[1]), ';'));
			foreach ($parts as $part) {
				$var = explode(':', $part);
				$key = self::SELECTOR . trim($var[0]);
				$value = trim($var[1]);
				$str = str_replace($key, $value, $str);
			}
			$str = preg_replace($regexp, '', $str);
			
			}
		return $str;
	}
	
	public static function mixinFilter($str)
	{
		$properties = self::matchProperty('mixin', $str);
		foreach ($properties as $prop)
		{
			$selectors = self::matchSelector('.' . $prop[2], $str);
			$selector = $selectors ? $selectors[0][2] : '';
			$str = str_replace($prop[0], $selector, $str);
		}
		return $str;
	}
}