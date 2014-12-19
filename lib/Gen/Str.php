<?php
/**
 * @category   Gen
 * @package	Gen_Str
 */
class Gen_Str
{
	const INDENT = "\t";
	const DELIMITER = '_';
	
	public static function indent($str, $nbr = 1)
	{
		$indent = null;
		for ($i=0; $i<$nbr; $i++) { 
			$indent .= self::INDENT;
		}
		
		return $indent . str_replace("\n", "\n$indent", $str);
	}
	
	public static function camelize($str, $lcfirst = true)
	{
		$parts = explode('_', $str);
		$str = null;
		foreach($parts as $part) {
			$str .= ($str || !$lcfirst)? ucfirst(mb_strtolower($part)) : mb_strtolower($part);
		}
		return $str;
	}
	
	public static function shorten($str, $limit = 50)
	{
		if (isset($str[$limit])) {
			$str = mb_substr($str, 0, $limit - 3, 'UTF-8') . "...";
		}
		return $str;
	}
	
	public static function underscore($str)
	{
		$str = str_replace('-', '_', $str);
		$str = preg_replace('#([A-Z]+)([A-Z][a-z])#', '$1_$2', $str);
		$str = preg_replace('#([a-z])([A-Z])#', '$1_$2', $str);
		
		return mb_strtolower($str);;
	}
	
	public static function dasherize($str)
	{
		return str_replace('_', '-', self::underscore($str));
	}
	
	public static function urlDasherize($str)
	{
		$str = self::transformAccents($str);
		$str = str_replace('-','_',$str);
		$str = preg_replace('#[^a-z0-9\._ \']#', '', mb_strtolower($str));
		$str = preg_replace('#[\._ \']#', '-', $str);
		$str = preg_replace('#[-]+#', '-', $str);
		return trim($str, '-');
	}
	
	public static function htmlify($str)
	{
		$str = '<p>' . _br($str) . '</p>';
		
		return $str;
	}
	
	public static function uniq($length = 6, $useDigits = true)
	{
		$digits = "0123456789";
		$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($useDigits) $characters .= $digits;
		
		$str = null;
		$max = strlen($characters) - 1;
		for ($i = 0; $i < $length; $i++) {
			$str .= $characters[mt_rand(0, $max)];
		}
		
		return $str;
	}
	
	public static function textify($str)
	{
		$str = preg_replace('/<(head|title|style|script)[^>]*>.*?<\/\\1>/s','',$str);
		$str = str_replace('<br />', "\n", $str);
		$str = str_replace('</p>', "\n", $str);
		
		return html_entity_decode(trim(strip_tags($str)));
	}
	
	public static function jsoninfy($str)
	{
		$str = htmlspecialchars($str, ENT_NOQUOTES, 'UTF-8');
		$str = preg_replace("/(\r\n|\n|\r)/", "\\n", $str);
		$str = preg_replace('#\\\*"#', '\\"', $str);
		
		return $str;
	}
	
	public static function transformAccents($str)
	{
		$accents_replacements = array(
			'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
			'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e',
			'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'ã' => 'a', 'å' => 'a',
			'À' => 'a', 'Â' => 'a', 'Ä' => 'a', 'Á' => 'a', 'Ã' => 'a', 'Å' => 'a',
			'î' => 'i', 'ï' => 'i', 'í' => 'i', 'ì' => 'i',
			'Î' => 'i', 'Ï' => 'i', 'Í' => 'i', 'Ì' => 'i',
			'ô' => 'o', 'ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'õ' => 'o',
			'Ô' => 'o', 'Ö' => 'o', 'Ò' => 'o', 'Ó' => 'o', 'Õ' => 'o',
			'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u',
			'Ù' => 'u', 'Û' => 'u', 'Ü' => 'u', 'Ú' => 'u',
			'ý' => 'y', 'Ý' => 'y', 'ÿ' => 'y',
			'ç' => 'c', 'Ç' => 'c',
			'ñ' => 'n', 'Ñ' => 'n'
		);
		
		$accents = array();
		$replacements = array();
		foreach($accents_replacements as $accent => $replacement) {
			array_push($accents, $accent);
			array_push($replacements, $replacement);
		}
		
		return str_replace($accents, $replacements, $str);
	}
	
	public static function keyify($str)
	{
		$str = self::urlDasherize($str);
		$str = str_replace('-', '_', $str);
		return $str;
	}
	
	public static function labelize($str)
	{
		$str = self::keyify($str);
		$str = ucwords(str_replace('_', ' ', $str));
		return $str;
	}
	
	public static function classify($str)
	{
		$str = self::camelize($str, false);
		$str = str_replace(' ', '_', ucwords(str_replace('::', ' ', $str)));
		return $str;
	}
	
	public static function constantize($str)
	{
		$str = mb_strtoupper($str);
		return $str;
	}
	
	public static function namespaceToFile($str, $suffix, $extension = '.php')
	{
		$str = self::camelize($str, false);
		$str = str_replace(' ', '/', ucwords(str_replace('::', ' ', $str)));
		return $str . $suffix . $extension;
	}
	
	public static function toFile($str, $suffix = '.php')
	{
		$str = str_replace('_', '/', $str);
		$str .= $suffix;
		
		return $str;
	}
	
	public static function pluralize($str)
	{
		$pattern = array('#y\b#', '#ess\b#', '#(s|h)\b#');
		$replace = array('ie','esse', '$1e');
		$str = preg_replace($pattern, $replace, $str) . 's';
		return $str;
	}
	
	public static function lcfirst($str)
	{
		$str = mb_strtolower(substr($str, 0, 1)) . substr($str, 1); 
		return $str;
	}
	
	public static function split($str)
	{
		$str = self::transformAccents($str);
		$str = mb_strtolower($str);
		$keywords = preg_split("/[\s,-.]+/", $str, -1, PREG_SPLIT_NO_EMPTY);
		return $keywords;
	}
	
	public function map($str, $data)
	{
		/**
		  * extract variables $var & $inflector for each tag
		  * a tag is delimited by {:tag}
		  * inflectors can be specified with `|`
		  */
		$tag = '[a-z][a-zA-Z.]*';
		$inflector = '\|[a-z][a-zA-Z| :"]*';
		$pattern = "{:($tag)($inflector)?}";
		
		preg_match_all('#' . $pattern . '#', $str, $matches);
		
		/** 
		  * compute recursive value from $data 
		  * these values are delimited by `.`
		*/
		foreach ($matches[1] as $key => $var) {
			$parts = explode('.', $var);
			$value = $data;
			foreach ($parts as $part) {
				$value = (object) $value;
				if (isset($value->$part)) {
					$value = $value->$part;
				} /** elseif (isset($default[$var])) {
					$value = $default[$var];
					break;
				} */ else {
					$value = '';
					break;
				}
			}
			/** replace :tag by computed $value */
			str_replace("{:$var}", (string) $value, $str);
		}
		return $str;
	}

	public static function toLower($str)
	{
		return mb_strtolower($str);
	}
	
	public static function substr($str, $start, $lenght = null)
	{
		return mb_substr($str, $start, $lenght, 'UTF-8');
	}
	
	public static function left($str, $lenght)
	{
		return self::substr($str, 0, $length);
	}
	
	public static function right($str, $lenght)
	{
		return self::substr($str, 0, - $length);
	}
}