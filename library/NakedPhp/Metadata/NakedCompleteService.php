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
 * @package    NakedPhp_Metadata
 */

namespace NakedPhp\Metadata;

/**
 * Decorates a NakedBareService object, providing wrapping of results after method calls.
 */
class NakedCompleteService implements NakedService
{
    /**
     * @var NakedService
     */
    private $_wrapped;

    public function __construct(NakedService $service)
    {
        $this->_wrapped = $service;
    }

    public function getClass()
    {
        return $this->_wrapped->getClass();
    }

    public function getClassName()
    {
        return $this->_wrapped->getClassName();
    }

    public function getMethods()
    {
        return $this->_wrapped->getMethods();
    }

    public function getMethod($methodName)
    {
        return $this->_wrapped->getMethod($methodName);
    }

    public function hasMethod($methodName)
    {
        return $this->_wrapped->hasMethod($methodName);
    }

    public function __call($methodName, array $arguments = array())
    {
        return $this->_wrapped->__call($methodName, $arguments);
    }
}

