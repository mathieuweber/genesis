<?php

class Gen_I18n
{
	protected static $_locale;
	
	protected static $_data = array();
	
	public static $_path;
	
	const DEFAULT_FILENAME = 'I18n';
	
	public static function setLocale($locale)
    {
		$oldLocale = self::getLocale();
		
        self::$_locale = $locale;
		self::$_data = self::loadFile();
		
		return $oldLocale;
    }
	
    public static function getLocale()
    {
        return self::$_locale;
    }
	
	public static function loadFile($locale = null)
    {
		$data = array();
		$path = self::getPath($locale);
		
		if (file_exists($path)) {
			include($path);
		}
		
		return $data;
    }
	
	public static function translate($text)
    {
		$key = md5(preg_replace('#\\\*"#', '"', $text));
        if (!isset(self::$_data[$key]) || self::$_data[$key] == '') {
			return $text;
		}
		
		return self::$_data[$key];
    }
	
	public static function getPath($locale = null, $fileName = null)
	{
		$locale = ($locale !== null) ? $locale : self::$_locale;
		return self::$_path . $locale . '/' . (($fileName === null) ? self::DEFAULT_FILENAME : $fileName) . '.php';
	}
	
	public static function write($locale = null, array $translations = array(), $overwrite = false, $fileName = null)
	{
		$cryptedTranslations = $overwrite ? array() : Gen_I18n::loadFile($locale);
		foreach($translations as $key => $translation) {
			$value = is_array($translation) ? $translation['suggestion'] : $translation;
			$key = preg_replace('#\\\*"#', '"', $key);
			
			$cryptedTranslations[md5($key)] = $value;
		}
		
		$path = self::getPath($locale, $fileName);
		$content = '<?php return array(';
		
		$first = true;
		foreach ($cryptedTranslations as $key => $value) {
			if ($value !== '') {
				$value = preg_replace('#\\\*"#', '\"', $value);
				$content .= ($first ? '' : ",\n") . "'" . $key . '\' => "' . $value . '"';
				$first = false;
			}
		}
		
		$content .= '); ?>';
		
		require_once('Gen/File.php');
		Gen_File::write($path, $content);
	}
	
	public static function writeScript($path, array $translations = array())
	{
		$content = 'i18n = {';
		
		$first = true;
		foreach ($translations as $translation) {
			if ($translation['suggestion'] !== '') {
				$key = preg_replace('#\\\*"#', '\"', $translation['message']);
				$value = preg_replace('#\\\*"#', '\"', $translation['suggestion']);
				
				$content .= ($first ? '' : ",\n") . '"' . $key . '" : "' . $value . '"';
				$first = false;
			}
		}
		
		$content .= '};';
		
		require_once('Gen/File.php');
		Gen_File::write($path, $content);
	}
	
	public static function writeXml($path, array $translations = array())
	{
		$content = '<xml>';
		
		$first = true;
		foreach ($translations as $translation) {
			if ($translation['suggestion'] !== '') {
				$key = preg_replace('#\\\*"#', '\"', $translation['message']);
				$value = preg_replace('#\\\*"#', '\"', $translation['suggestion']);
				
				$content .= ($first ? '' : ",\n") . '<translation key="' . $key . '" value="' . $value . '"/>';
				$first = false;
			}
		}
		
		$content .= '</xml>';
		
		require_once('Gen/File.php');
		Gen_File::write($path, $content);
	}
}