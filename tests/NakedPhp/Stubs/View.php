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
 * @package    NakedPhp_Stubs
 */

namespace NakedPhp\Stubs;
use Exception;

class View implements \Zend_View_Interface
{
    /**
     * @var array   assigned variables
     */
    private $_variables;

    /**
     * @var array   mocked view helpers
     */
    private $_helpers;

    public function __isset($key)
    {
        return isset($this->_variables[$key]);
    }

    public function __set($key, $val)
    {
        $this->_variables[$key] = $val;
    }

    public function __get($key)
    {
        return $this->_variables[$key];
    }

    public function __unset($key)
    {
        unset($this->_variables[$key]);
    }

    /**
     * @param string $name      name of view helper
     * @param object $object    nearly everything that has a $name method
     */
    public function setHelper($name, $object)
    {
        $name = lcfirst($name);
        $this->_helpers[$name] = $object;
    }

    public function __call($method, $args)
    {
        $name = lcfirst($method);
        return call_user_func_array(array($this->_helpers[$name], $name), $args);
    }

    public function addBasePath($path, $classPrefix = 'Zend_View') {}
    public function assign($spec, $value = null) {}
    public function clearVars () {}
    public function getEngine() {}
    public function getScriptPaths() {}
    public function render($name) {}
    public function setBasePath($path, $classPrefix = 'Zend_View') {}
    public function setScriptPath($path) {}
}
