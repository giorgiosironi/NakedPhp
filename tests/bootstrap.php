<?php
/**
 * Naked Php is a framework that implements the Naked Objects pattern.
 * @copyright Copyright (C) 2009  Giorgio Sironi
 * @license http://www.gnu.org/licenses/lgpl-2.1.txt 
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * @category   NakedPhp
 */

$library = realpath(__DIR__ . '/../library');
ini_set('include_path', __DIR__ . PATH_SEPARATOR . $library . PATH_SEPARATOR . ini_get('include_path'));

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
require_once 'Doctrine/Common/ClassLoader.php';
$doctrineAutoloader = new \Doctrine\Common\ClassLoader();
