<?php
require_once('Gen/Translate/Pattern.php');

class Gen_Translate
{
	public static function findPhrases($folder, $pattern, $date = null)
	{
		$result = array();
		
		$result = array_merge($result, self::parseFolder($folder, $pattern, $date));
		$result = array_merge($result, self::parseFolder($folder, Gen_Translate_Pattern::CONTEXT, $date));
		$result = array_merge($result, self::parseFolder($folder, Gen_Translate_Pattern::PLURAL, $date));
		
		return $result;
	}
	
	public static function parseFolder($folder, $pattern, $date = null, array $phrases = array())
	{
		//Warnings are not displayed when folder is not a folder but a file
		//We then set folder as the dirname of folder whith only one file, the basename of folder
		$folders = @scandir($folder);
		if ($folders == false) {
			$folders = (array) basename($folder);
			$folder = dirname($folder) . '/';
		}
		
		foreach($folders as $file) {
			$filePath = $folder . $file;
			if (is_dir($filePath) && ($file != '.') && ($file != '..') && ($file != '.svn')) {
				$phrases = self::parseFolder($filePath .'/', $pattern, $date, $phrases);
			} elseif(strpos($file, '.' . Gen_Translate_Pattern::getExtensionById($pattern)) && ($date === null || filemtime($filePath) > strtotime($date))) {
				$phrases = array_merge($phrases, self::parseFile($filePath, $pattern));
			}
		}
		
		return $phrases;
	}
	
	public static function parseFile($filePath, $pattern)
	{
		$phrases = array();
		$content = file_get_contents($filePath);
		$condition = Gen_Translate_Pattern::getConditionById($pattern);
		
		if (!$condition || preg_match("#" . $condition . "#", $content)) {
			preg_match_all(Gen_Translate_Pattern::getPatternById($pattern), $content, $matches);
			
			foreach($matches[0] as $id => $match) {
				$message = preg_replace('#\\\*"#', '"', $matches[1][$id]);
				
				$context = ($pattern == Gen_Translate_Pattern::CONTEXT && isset($matches[2])) ? $matches[2][$id] : '';
				$key = ($context && $pattern == Gen_Translate_Pattern::CONTEXT) ? ('{context:' . $context . '}' . $message) : $message;
				$phrases[md5($key)] = self::parse($pattern, $message, $context, $filePath);
				
				if ($pattern == Gen_Translate_Pattern::PLURAL) {
					$message = preg_replace('#\\\*"#', '"', $matches[2][$id]);
					$phrases[md5($message)] = self::parse($pattern, $message);
				}
			}
		}
		
		return $phrases;
	}
	
	public static function parse($pattern, $message, $context = null, $filePath = null)
	{
		$phrase['message'] = $message;
		if ($context !== null) $phrase[Gen_Translate_Pattern::getKeyById($pattern)] = $context;
		if ($pattern == Gen_Translate_Pattern::JAVASCRIPT) {
			$phrase[Gen_Translate_Pattern::getKeyById(Gen_Translate_Pattern::CONTEXT)] = Gen_Translate_Pattern::getKeyById($pattern);
		} elseif ($pattern == Gen_Translate_Pattern::FLASH) {
			$phrase[Gen_Translate_Pattern::getKeyById(Gen_Translate_Pattern::CONTEXT)] = strtolower(basename($filePath, '.xml'));
		}
		
		return $phrase;
	}
}