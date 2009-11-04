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
 * Wraps a NakedBareEntity object, providing automatic injection of services
 * as methods parameters (Decorator pattern).
 * Should not be serialized. Store the inner object instead (@see getBareEntity()).
 */
class NakedCompleteEntity implements NakedEntity
{
    protected $_entity;
    protected $_merger;

    public function __construct(NakedBareEntity $entity = null, MethodCaller $methodCaller = null)
    {
        $this->_entity = $entity;
        $this->_merger = $methodCaller;
    }

    /**
     * @return NakedEntityClass
     */
    public function getClass()
    {
        return $this->_entity->getClass();
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->_entity->getClassName();
    }

    /**
     * @return NakedBareEntity
     */
    public function getBareEntity()
    {
        return $this->_entity;
    }

    public function __toString()
    {
        return $this->_entity->__toString();
    }

    /**
     * @return array    proxies to wrapped entity
     */
    public function getState()
    {
        return $this->_entity->getState();
    }

    /**
     * @param array $data   field names are keys; works also with objects and
     *                      objects wrapped in NakedBareEntity
     */
    public function setState(array $data)
    {
        return $this->_entity->setState($data);
    }

    public function getMethods()
    {
        return $this->_merger->getApplicableMethods($this->_entity->getClass());
    }

    /**
     * Convenience method.
     */
    public function getMethod($methodName)
    {
        $methods = $this->getMethods();
        return $methods[$methodName];
    }

    /**
     * Convenience method.
     */
    public function hasMethod($methodName)
    {
        $methods = $this->getMethods();
        return isset($methods[$methodName]);
    }

    public function hasHiddenMethod($methodName)
    {
        return $this->_merger->hasHiddenMethod($this->_entity->getClass(), $methodName);
    }

    public function __call($methodName, array $arguments = array())
    {
        return $this->_merger->call($this->_entity, $methodName, $arguments);
    }

    public function getIterator()
    {
        return $this->_entity->getIterator();
    }
}
