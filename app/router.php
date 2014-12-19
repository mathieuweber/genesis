<?php
Gen_Log::log('Start', 'Router');
require_once('Gen/Controller/Router.php');

$r = new Gen_Controller_Router();

$cacheFileName = CACHE_DIR.'Gen_Controller_Router_'.APP_VERSION.'.php';

if(file_exists($cacheFileName)) {
	include($cacheFileName);
	$r->setRoutes($routes);
	Gen_Log::log('Use of Cache', 'Router');
} else {
	$id = '[0-9]+';
	$dasherized = '[a-z0-9-]+';
	$rootDir    = APP_DIR . 'routes';
	
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
	$r->addRoute('backoffice', 'backoffice', array('controller' => 'backoffice', 'action' => 'index'));
	$r->addRoute('default', '', array('controller' => 'index', 'action' => 'index'));
	$routes = $r->getRoutes();
	Gen_File::write($cacheFileName, '<?php $routes = ' . var_export($routes, true).';');
	Gen_Log::log('Build routes and cache', 'Router');
}