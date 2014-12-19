<?php

/****************
 *		Log		*
 ****************/
require_once('Gen/Log.php');
Gen_Log::$_debug = APP_DEBUG;
Gen_Log::log('Initialization', 'init.php');

/***************************
 *     PDO / Dao config    *
 ***************************/
require_once('Gen/Dao/Abstract.php');
Gen_Dao_Abstract::config(DB_DRIVER, DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);

/********************
 *		I18n		*
 ********************/
require_once('Gen/I18n.php');
Gen_I18n::$_path = LOCALE_DIR;
Gen_I18n::setLocale(APP_DEFAULT_LANG);

/***************************
 * Front Controller config *
 ***************************/
require_once ('Gen/Controller/Front.php');
$frontController = Gen_Controller_Front::getInstance();

Gen_Controller_Front::$env = APP_ENV;
Gen_Controller_Front::$debug = APP_DEBUG;
Gen_Controller_Front::$appName = APP_NAME;
Gen_Controller_Front::$appMail = APP_MAIL;
Gen_Controller_Front::$controllerDir = APP_CONTROLLER_DIR;
Gen_Controller_Front::setViewDir(APP_VIEW_DIR);

/***************************
 *      Router config      *
 ***************************/
include(APP_DIR . 'router.php');
$r->setBaseUrl(BASE_URL);
$r->setServerName(SERVER_NAME);
$frontController->setRouter($r);

/************************
 *		Acl config		*
 ************************/
include(APP_DIR . 'acl.php');
$frontController->setAcl($a);

/****************************
 *		Observer config		*
 ****************************/
require_once ('Gen/Controller/Event/Dispatcher.php');
Gen_Controller_Event_Dispatcher::$observerDir = APP_CONTROLLER_DIR;
include(APP_DIR. 'event_dispatcher.php');
$frontController->setEventDispatcher($e);