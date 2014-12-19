<?php

try{
	include(dirname(__FILE__).'/conf/const.php');
	
	include(dirname(__FILE__).'/init.php');
	// if (APP_DEBUG) {
		// Gen_Log::log('Gen_Css', 'index.php');
		// require_once('Gen/Css.php');
		// Gen_Css::$cacheDir = MS_STYLE_DIR;
		// Gen_Css::$debug = isset($_GET['force_css']) ? true : false;
		// Gen_Css::process('manolosanctis.css');
	// }
	Gen_Log::log('Gen_Controller_Front::run', 'index.php');
	
	include(APP_VIEW_DIR.'helpers.php');
	
	Gen_Controller_Front::run();
	
	if(APP_DEBUG) {
		if (isset($_GET['show_log']) && $_GET['show_log'] == true) {
			Gen_Log::stop();
			Gen_Log::log($_POST,'$_POST');
			Gen_Log::log($_FILES,'$_FILES');
			Gen_Log::log($_SERVER,'$_SERVER');
			Gen_Log::log($_SERVER,'$_COOKIES');
			Gen_Log::log($_SESSION,'$_SESSION');
			Gen_Log::log(get_included_files(),'Included Files');
			echo '<div class="block" style="width: 1000px; margin: 20px auto; padding: 20px; border: 1px solid #acacac; background-color: white; color: black;">',
			Gen_Log::render(),
			'</div>';
		}
	}

} catch (Exception $e) {
    echo '<h1>Critical Internal Error</h1>';
	if(APP_DEBUG) {
        echo "<h3 style=\"color:red\">Error</h3>"
       . "<p><pre>{$e->getMessage()}</pre></p>"
       . "<p><pre>{$e->getTraceAsString()}</pre></p>";
    }
}

