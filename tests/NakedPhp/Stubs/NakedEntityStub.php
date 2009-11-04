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
use NakedPhp\Metadata\NakedBareEntity;
use NakedPhp\Metadata\NakedMethod;

class NakedEntityStub extends NakedBareEntity
{
    protected $_hiddenMethods = array();
    protected $_className;
    protected $_state;

    public function getClassName()
    {
        return $this->_className;
    }

    public function setClassName($className)
    {
        $this->_className = $className;
    }

    public function setState(array $state)
    {
        $this->_state = $state;
    }

    public function getState()
    {
        return $this->_state;
    }
    
    public function addHiddenMethod($methodName)
    {
        $this->_hiddenMethods[$methodName] = true;
    }

    public function hasHiddenMethod($methodName)
    {
        return isset($this->_hiddenMethods[$methodName]);
    }
}
