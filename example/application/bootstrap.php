<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__)));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../../library'),
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';  

require_once 'NakedPhp/Loader.php';
$loader = new NakedPhp\Loader();

require_once 'Zend/Loader/Autoloader.php';
$zfLoader = Zend_Loader_Autoloader::getInstance();
$zfLoader->pushAutoloader(array($loader, 'autoload'));

/*
require 'Doctrine/Common/ClassLoader.php';
$classLoader = new \Doctrine\Common\ClassLoader();
*/


$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
    'basePath'  => APPLICATION_PATH,
    'namespace' => 'Example'
));
$resourceLoader->addResourceType('model', 'models/', 'Model');

