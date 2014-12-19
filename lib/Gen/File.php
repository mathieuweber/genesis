<?php
class Gen_File
{
	const RSS_DELAY = 30;
	
	const SYNC_NEW = 1;
	const SYNC_RECENT = 2;
	const SYNC_ALL = 3;
	
	public static function getModeLabel($mode)
	{
		switch($mode) {
			case self::SYNC_NEW:
				return 'SYNC_NEW';
				
			case self::SYNC_RECENT:
				return 'SYNC_RECENT';
				
			case self::SYNC_ALL:
				return 'SYNC_ALL';
		}
		return 'Does not exist';
	}
	
	public static function hasExpired($filePath, $hours = 24)
	{
		if (!file_exists($filePath)) return true;
		
		$diff = date_diff(new DateTime(date('Y-m-d H:i:s', filemtime($filePath))), new DateTime('now'));
		return (($diff->y * 365 * 24 * 60) + ($diff->m * 30 * 24 * 60) + ($diff->d * 24 * 60) + ($diff->h * 60) + $diff->i)/60 >= $hours;
	}
	
	public static function copy($src, $dest)
	{
		$dir = dirname($dest);
		
		if (!file_exists($src) || !file_exists($dir) && !mkdir($dir, 0777, true)) {
			return false;
		}
		return copy($src, $dest);
	}
	
	public static function sync($src, $dest, $mode = self::SYNC_NEW)
	{
		if(!file_exists($dest) || ((filemtime($src) > filemtime($dest)) && $mode == self::SYNC_RECENT) || ($mode == self::SYNC_ALL)) {
			return self::copy($src, $dest);
		}
		return false;
	}
	
	public static function write($filePath, $content = '', $override = true)
	{
		$dir = dirname($filePath);
		
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
		
		$file = null;
		
		if (!file_exists($filePath)) {
			$file = fopen($filePath,'x+');
		} elseif($override) {
			$file = fopen($filePath,'w+');
		} else {
			return false;
		}
		
		if(false !== $file) {
			fwrite($file, trim($content));
			fclose($file);
			return true;
		}
		return false;
	}
	
	public static function read($filePath)
	{
		return file_get_contents($filePath);
	}
	
	public static function remove($fileName)
	{
		self::delete($fileName);
	}
	
	public static function delete($fileName)
	{
		if(!file_exists($fileName)) {
			return false;
		}
		if(is_dir($fileName)) {
			$dir = opendir($fileName);
			while(false !== ($file = readdir($dir)) ) {
				if (($file == '.' ) || ($file == '..')) {
					continue;
				}
				self::delete($fileName.'/'.$file);
			}
			return rmdir($fileName);
		}
		return unlink($fileName);
	}
	
	public static function move($srcFile, $destFile)
	{
		if(self::copy($srcFile, $destFile)) {
			return self::delete($srcFile);
		}
		return false;
	}
	
	public static function rss_parser($url) {
		$rss = false;
		$stream = '';
		
		$handle = fopen($url, "rb");
		if (false !== $handle) {
			$stream = stream_get_contents($handle);
			$stream = str_replace('dc:date', 'pubDate', $stream);
			$rss = @simplexml_load_string($stream);
			fclose($handle);
		}
		return $rss;
	}
	
	/**
	 * Note to do your own check to make sure the directory exists that you first call it on.
	 */
	public static function recurse_copy($src,$dst) {
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ($file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if (is_dir($src . '/' . $file)) {
					recurse_copy($src . '/' . $file, $dst . '/' . $file);
				} else {
					copy($src . '/' . $file, $dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}
	
	public static function listDir($rootDir)
	{
		$dirs = (array) $rootDir;
		if($files = scandir($rootDir)) {
			foreach($files as $file) {
				$dir = $rootDir.'/'.$file;
				if(($file != '.') && ($file != '..') && is_dir($dir)) {
					$dirs = array_merge($dirs, self::listDir($dir));
				}
			}
		}
		return $dirs;
	}
	
	public static function recursiveSync($src, $dest, $mode = self::SYNC_NEW)
	{
		$results = array();
		if($files = scandir($src)) {
			foreach($files as $file) {
				$srcFile = $src.'/'.$file;
				$destFile = $dest.'/'.$file;
				if(($file != '.') && ($file != '..')) {
					if(is_dir($srcFile)) {
						$results = array_merge($results, self::recursiveSync($srcFile, $destFile, $mode));
					} else {
						if(self::sync($srcFile, $destFile, $mode)) {
							$results[] = $file;
						}
					}
				}
			}
		}
		return $results;
	}
	
	/**
	 * @param string $fileName
	 * @return array $data
	 */
	public static function readCsv($fileName, $options = array()){
		$options = array_merge(array(
			'delimiter' => ';',
			'enclosure' => ' ',
			'escape' => '\\',
			'lenght' => 1000,
			'header' => true
		), $options);
		
		if(($handle = fopen($fileName, 'r')) == false) {
			return false;
		}
		
		$data = array();
		$first = true;
		while($row = fgetcsv($handle, $options['lenght'], $options['delimiter'], $options['enclosure'], $options['escape'])) {
			switch (true) {
				case $options['header'] == true && $first:
					$headers = $row;
					$first = false;
					break;
			case $options['header'] == true:
				foreach($headers as $k => $col) {
					$tmp[$col] = isset($row[$k])?$row[$k]:null; 
				}
				$row = $tmp;
			default:
				$data[] = $row;
				break;
			}
		}
		fclose($handle);
		return $data;
	}
	
	public static function encodeCsv($data, $options = array()){
		$options = array_merge(array(
			'delimiter' => ';',
			'enclosure' => '"',
			'lenght' => 1000,
			'header' => true
		), $options);
		$csv = '';
		$first = true;
		$header = '';
		foreach($data as $row)
		{
			foreach($row as $key => $col)
			{
				if($options['header'] && $first) {
					$header .= self::_escapeCsv($key, $options).$options['delimiter'];
				}
				$csv .= self::_escapeCsv($col, $options).$options['delimiter'];
			}
			$csv = rtrim($csv, $options['delimiter'])."\n";
			$first = false;
		}
		if($options['header']) {
			$csv = $header."\n".$csv;
		}
		return $csv;
	}
	
	private static function _escapeCsv($text, array $options)
	{
		$text = str_replace(array("\n", "\r", "\n\r"), " ", $text);
		$text = utf8_decode($text);
		$text = str_replace($options['enclosure'], $options['enclosure'].$options['enclosure'], $text);
		return $options['enclosure'].$text.$options['enclosure'];
	}
}