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
use NakedPhp\Service\MethodCaller;

/**
 * Decorates a NakedBareService object, providing wrapping of results after method calls.
 */
class NakedCompleteService implements NakedService
{
    /**
     * @var NakedService
     */
    private $_wrapped;

    /**
     * @var MethodCaller
     */
    private $_caller;

    public function __construct(NakedService $service, MethodCaller $methodCaller = null)
    {
        $this->_wrapped = $service;
        $this->_caller = $methodCaller;
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
        return $this->_caller->call($this->_wrapped, $methodName, $arguments);
    }

    public function __toString()
    {
        return (string) $this->_wrapped;
    }

    /**
     * {@inheritdoc}
     * Not allowed.
     */
    public function addFacet(Facet $facet)
    {
        throw new \Exception('Adding a Facet to an object is not allowed. Access the NakedClass instance instead.');
    }

    /**
     * {@inheritdoc}
     * Proxies to the wrapped service.
     */
    public function getFacet($type)
    {
        return $this->_wrapped->getFacet($type);
    }
}

