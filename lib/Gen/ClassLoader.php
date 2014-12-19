<?php
require_once('Gen/Str.php');

class Gen_ClassLoader
{
	/**
	 * 
	 * @param String $className ex: Theme
	 * @param String $moduleName ex:Album
	 * @param String $suffix ex:Controller
	 * @param String $directoryPath ex:'./application/Controller/
	 */
	public static function loadClass($classKey, $moduleName, $suffix, $directoryPath)
	{
		if($moduleName) {
			$moduleName = Gen_Str::camelize($moduleName, false);
		}
		
		$className = (string) self::getClassName($classKey, $suffix);
		
		/** perform action only if class not already loaded */
		if (!class_exists($className, false)) {

			$fileName = self::getFileName($classKey, $suffix);
			
			$file = $directoryPath . ($moduleName ? $moduleName . '/' : '' ) . $fileName;
			if (file_exists($file)) {
				require($file);
				return $className;
			}
			Gen_Log::log('File Not Found: ' . $file, 'Gen_ClassLoader::loadClass', 'warning');
			return false;
		}
		return $className;
	}
	
	/**
	  * Format a File name
	  *
	  * @param  string $controller key
	  * @return Gen_String
	  */
	public static function getFileName($str, $suffix)
	{
		return Gen_Str::namespaceToFile($str, $suffix);
	}
	
	/**
	  * Format a ClassName
	  *
	  * @param  sting $controller key
	  * @return Gen_String
	  */
	public static function getClassName($str, $suffix)
	{
		return Gen_Str::classify($str . '_' . $suffix);
	}
}