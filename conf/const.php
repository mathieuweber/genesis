<?php
// BASE
define('SERVER_NAME', $_SERVER['HTTP_HOST']);
define('ROOT_DIR', realpath(dirname(__FILE__) .'/..') . '/');
define('APP_DIR', ROOT_DIR . 'app/');
define('LIB_DIR', ROOT_DIR . 'lib/');
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . LIB_DIR . PATH_SEPARATOR . APP_DIR);

// APP
define('BASE_URL', '/');
define('APP_NAME', 'Genesis');
define('APP_BASELINE', 'PHP MVC Framework');
define('APP_DEFAULT_DESCRIPTION', 'PHP MVC Framework');
define('APP_DEFAULT_LANG', 'en');
define('APP_KEY', 'genesis');
define('APP_ENV', 'LOCAL');
define('APP_MAIL', '');
define('APP_DEBUG', true);
define('SEND_MAIL', false);
define('GOOGLE_ANALYTICS_KEY', null);


// DB
define('DB_DRIVER',"mysql");
define('DB_HOST',"127.0.0.1");
define('DB_NAME',"db_name");
define('DB_USER',"root");
define('DB_PASSWORD',"");
define('DB_PREFIX',"");

//DIR
define('APP_CONTROLLER_DIR', APP_DIR . 'Controller/');
define('APP_OBSERVER_DIR', APP_DIR . 'Observer/');
define('APP_VIEW_DIR', APP_DIR . 'view/');
define('LOCALE_DIR', ROOT_DIR . 'locale/');
define('TMP_DIR', ROOT_DIR . 'tmp/');
define('CACHE_DIR', ROOT_DIR . 'cache/');
define('PUBLIC_DIR', ROOT_DIR . 'public/');
define('IMG_DIR', PUBLIC_DIR . 'img/');
define('FILE_DIR', PUBLIC_DIR . 'files/');

// URL
define('SERVER_URL', 'http://' . SERVER_NAME);
define('PUBLIC_URL', BASE_URL . 'public/');
define('STYLE_URL', PUBLIC_URL . 'styles/');
define('SCRIPT_URL', PUBLIC_URL . 'scripts/');
define('IMG_URL', PUBLIC_URL . 'img/');
define('FILE_URL', PUBLIC_URL . 'files/');
define('JS_LIB_URL', SERVER_URL . '/lib/js/');
define('CSS_LIB_URL', SERVER_URL . '/lib/css/');

//CONFIG
define('APP_VERSION', '0.1');