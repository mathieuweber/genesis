<?php
Gen_Log::log('Start', 'Acl');
require_once('Gen/Controller/Acl.php');

$a = new Gen_Controller_Acl();

$cacheFileName = CACHE_DIR.'Gen_Controller_Acl_'.APP_VERSION.'.php';

if(file_exists($cacheFileName)) {
	include($cacheFileName);
	$a->setAuthorizations($authorizations);
	Gen_Log::log('Use of Cache', 'Acl');
} else {
	$rootDir = APP_DIR . 'acl';
	
	if(file_exists($rootDir)) {
		require_once('Gen/File.php');
		$dirs = Gen_File::listDir($rootDir);
		foreach($dirs as $dir)
		{
			if($files = scandir($dir)) {
				foreach($files as $file) {		
					if($file != '.' ||$file != '..') {
						$split = explode('.',$file);
						$ext = strtolower(array_pop($split));
						if($ext == 'php') {
							include($dir.'/'.$file);
						}
					}
				}
			}
		}
		$authorizations = $a->getAuthorizations();
		Gen_File::write($cacheFileName, '<?php $authorizations = ' . var_export($authorizations, true).';');
		Gen_Log::log('Build authorizations and cache', 'Acl');
	} else {
		Gen_Log::log('No File', 'Acl');
	}
}