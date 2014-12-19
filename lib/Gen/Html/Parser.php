<?php

class Gen_Html_Parser 
{
	const VALIDATOR_CSS_UNIT = '(([\+\-]?[0-9\.]+)(em|ex|px|in|cm|mm|pt|pc|\%))|0';
	const VALIDATOR_URL = 'http://\\S+';
	const VALIDATOR_CSS_PROPERTY = '[a-z\-]+';
	const VALIDATOR_STYLE = '[^"]*';
	
	protected static $_tags = 'a|b|blockquote|br|cite|d[ldt]|em|h[1-6]|i|img|li|ol|p|span|strong|u|ul';
	
	protected static $_selfClosingTags = array('area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta', 'col', 'param');
	
	protected static $_attributePattern = '@\s([-\w]+)\s*=\s*[\"\']([^"\']+)[\"\']@xsi';
	
	protected static $_attributes = array(
		'img' => array(
			'width' => '[0-9]+',
			'height' => '[0-9]+',
			'src' => self::VALIDATOR_URL,
			'style' => self::VALIDATOR_STYLE
			),
		'span' => array(
			'style' => self::VALIDATOR_STYLE
			),
		'p' => array(
			'style' => self::VALIDATOR_STYLE
			),
		'a'	=>	array(
			'href' => self::VALIDATOR_URL
			)
	);
	
	protected static $_styleValidators = array(
		'color' => '(\#[a-fA-F0-9]+)|([a-z ]+)',
		'background-color' => '\#[a-zA-Z0-9]+',
		'font-style' => '(normal|italic|oblique)',
		'font-size' => '[\-a-z]+',
		'margin-left' => self::VALIDATOR_CSS_UNIT,
		'margin-right' => self::VALIDATOR_CSS_UNIT,
		'text-align' => '(left|right|center|justify)',
		'text-indent' => self::VALIDATOR_CSS_UNIT,
		'text-decoration' => '(none|overline|underline|blink|line-through)',
		'width' => self::VALIDATOR_CSS_UNIT,
		'height' => self::VALIDATOR_CSS_UNIT
	);
	
	public static function stripTags($str, $tags = null)
	{
		if (is_array($tag)) {
            $tag = implode('|', $tag);
        }
		$pattern = '#</?('. $tags .')\s[^>]+/?>#';
		
		$str = preg_replace($pattern, '', $str);
		return $str;
	}
	
	public static function stripComments($str)
	{
		$pattern = '#<!--.*?-->#';
		$str = preg_replace($pattern, '', $str);
		return $str;
	}
	
	public static function specialchars($str)
	{
		$str = str_replace('<', '&lt;', $str);
		$str = str_replace('>', '&gt;', $str);
		return $str;
	}
	
	public static function sanitize($str)
	{
		$tokens = array();
		
		//tokenize opening tags with no attributes
		$pattern = '#<(/)?('. self::$_tags .')>#';
		$replace = '__SAFE_TAG_$1$2__';
		$str = preg_replace($pattern, $replace, $str);
		
		// tokenize tags with attributes
		$pattern = '#<('. self::$_tags .')(?:\s+(?:[a-z]+)="(?:[^"\\\]*(?:\\\"[^"\\\]*)*)")*\s*(/)?>#';
		preg_match_all($pattern, $str, $matches, PREG_SET_ORDER);
		foreach($matches as $i => $match) {
			$tokens[$i] = self::cleanTag($match[1], $match[0]);
			$str = str_replace($match[0], '__SAFE_TOKEN_'.$i.'__', $str);
		}
		
		$str = self::stripComments($str);
		$str = self::stripTags($str);
		$str = self::specialchars($str);
		
		foreach ($tokens as $i => $cleanTag) {
			$str = str_replace('__SAFE_TOKEN_'.$i.'__', $cleanTag, $str);
		}
		
		$pattern = '#__SAFE_TAG_(/?(?:'. self::$_tags .'))__#';
		$replace = '<$1>';
		$str = preg_replace($pattern, $replace, $str);
		
		return $str;
	}
	
	public static function cleanTag($tag, $str)
	{
		$cleanTag = '<' . $tag;
		
		if ($tag === 'a') {
			$cleanTag .= ' rel="nofolow" target="_blank"';
		}
		
		if (isset(self::$_attributes[$tag])) {
			foreach(self::$_attributes[$tag] as $attr => $attrPattern) {
				$pattern = '#'.$attr.'="('. $attrPattern .')"#';
				preg_match($pattern, $str, $match);
				if (isset($match[1])) {
					if ($attr == 'style') {
						$cleanTag .= ' style="' . self::cleanStyle($match[1]) . '"';
					} else {
						$cleanTag .= ' ' . $attr . '="' . $match[1] . '"';
					}
				}
			}
		}
		
		if ($tag === 'img') {
			$cleanTag .= ' /';
		}
		
		$cleanTag .= '>';
		return $cleanTag;
	}
	
	public static function cleanStyle($style)
	{
		$cleanStyle = '';
		
		foreach(self::$_styleValidators as $stl => $stlPattern) {
			$pattern = '#[; ]?' . $stl . '\s*:\s*(' . $stlPattern . ')\s*;#i';
			preg_match($pattern, $style, $match);
			if (isset($match[1])) {
				$cleanStyle .= ($cleanStyle ? ' ' : '') . $stl . ':' . $match[1] . ';';
			}
		}
		
		return $cleanStyle;
	}
	
	public static function extractTags($str, $tag, $selfClosing = null)
	{
		if (is_array($tag)) {
            $tag = implode('|', $tag);
        }
		
		if (is_null($selfClosing)) {
            $selfClosing = in_array($tag, self::$_selfClosingTags);
        }
		
		$pattern = $selfClosing
				? ('@<('. $tag .')(\s*[^>]*)\s*/?>@xsi')
				: ('@<('.$tag.')([^>]*)>(.*)</'.$tag.'>@xsi');
		$matches = array();
		preg_match_all($pattern, $str, $matches, PREG_SET_ORDER);
		$results = array();
		foreach($matches as $i => $match) {
			$results[$i] = array(
				'tag' => strtolower($match[1]),
				'attributes' => self::extractAttributes($match[2]),
				'content' => isset($match[3]) ? trim($match[3]) : '',
				'html' => $match[0]
			);
		}
		return $results;
	}
	
	public static function extractAttributes($str)
	{
		$results = array();
		$matches = array();
		preg_match_all(self::$_attributePattern, $str, $matches, PREG_SET_ORDER);
		foreach($matches as $match) {
			$property = strtolower($match[1]);
			$results[$property] = trim($match[2]);
		}
		return $results;
	}
	
	public static function parse($str, $url = null)
	{
		$url = urldecode($url);
		$results = array();
		 
		$base_url = substr($url,0, strpos($url, "/",8));
		$relative_url = substr($url,0, strrpos($url, "/")+1);

		/*$str = str_replace(array("\n","\r","\t",'</span>','</div>'), '', $str);

		$str = preg_replace('/(<(div|span)\s[^>]+\s?>)/',  '', $str);*/
		if (mb_detect_encoding($str, "UTF-8") != "UTF-8") {
			$str = utf8_encode($str);
		}
		
		// Parse Title
		$nodes = self::extractTags($str, 'title');
		$results['title'] = $nodes[0]['content'];
		
		$base_override = false;		
		/*/ Parse Base
		
		$base_regex = '/<base[^>]*'.'href=[\"|\'](.*)[\"|\']/Ui';
		preg_match_all($base_regex, $str, $base_match, PREG_PATTERN_ORDER);
		if(strlen($base_match[1][0]) > 0)
		{
			$base_url = $base_match[1][0];
			$base_override = true;
		}*/
		 
		// Parse Description
		$results['description'] = '';
		$nodes = self::extractTags($str, 'meta');
		foreach($nodes as $node)
		{
			if(isset($node['attributes']['name']) && $node['attributes']['name'] == 'description') {
				$results['description'] = $node['attributes']['content'];
			}
			if(isset($node['attributes']['property'])) {
				switch (($node['attributes']['property']))
				{
					case 'og:title':
						$results['fb_title'] = $node['attributes']['content'];
						break;
					
					case 'og:description':
						$results['fb_description'] = $node['attributes']['content'];
						break;
					
					case 'og:image':
						$results['fb_image'] = $node['attributes']['content'];
						break;
				}
			}
		}
		 
		// Parse Images
		$imageTags = self::extractTags($str, 'img');
		$images = array();
		for ($i=0;$i<=sizeof($imageTags);$i++)
		{
			$img = @$imageTags[$i]['attributes']['src'];
			$width = isset($imageTags[$i]['attributes']['width']) ? $imageTags[$i]['attributes']['width'] : null;
			$height = isset($imageTags[$i]['attributes']['height']) ? $imageTags[$i]['attributes']['height'] : null;
			// $width = preg_replace("/[^0-9.]/", '', $imageTags[$i]['attributes']['width']);
			// $height = preg_replace("/[^0-9.]/", '', $imageTags[$i]['attributes']['height']);
		 
			$ext = trim(pathinfo($img, PATHINFO_EXTENSION));
		 
			if($img && $ext != 'gif')
			{
			  if (substr($img,0,7) == 'http://')
				 ;
			  else  if (substr($img,0,1) == '/' || $base_override)
				 $img = $base_url . $img;
			  else
				 $img = $relative_url . $img;
		 
			  if ($width == '' && $height == '')
			  {
				 $details = @getimagesize($img);
		 
				 if(is_array($details))
				 {
					list($width, $height, $type, $attr) = $details;
				 }
			  }
			  $width = intval($width);
			  $height = intval($height);
		 
			  if ($width > 199 || $height > 199)
			  {
				 if (
					(($width > 0 && $height > 0 && (($width / $height) < 3) && (($width / $height) > .2))
						|| ($width > 0 && $height == 0 && $width < 700)
						|| ($width == 0 && $height > 0 && $height < 700)
					)
					&& strpos($img, 'logo') === false)
				 {
					$images[] = array('src' => $img, 'width' => $width, 'height' => $height);
				 }
			  }
		 
			}
		}
		if(isset($results['fb_image'])) {
			array_unshift($images, array('src' => $results['fb_image'], 'width' => 200, 'height' => 200));
		}
		$results['images'] = $images;
		return $results;
	}
}