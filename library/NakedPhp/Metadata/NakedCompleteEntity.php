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
use NakedPhp\Service\NakedFactory;

/**
 * Wraps a NakedBareEntity object, providing automatic injection of services
 * as methods parameters (Decorator pattern).
 * Should not be serialized. Store the inner object instead (@see getBareEntity()).
 */
class NakedCompleteEntity implements NakedEntity
{
    protected $_entity;
    protected $_caller;

    public function __construct(NakedBareEntity $entity = null, MethodCaller $methodCaller = null)
    {
        $this->_entity = $entity;
        $this->_caller = $methodCaller;
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
     * {@inheritdoc}
     * Proxies to wrapped entity.
     */
    public function getState()
    {
        return $this->_entity->getState();
    }

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity.
     */
    public function setState(array $data)
    {
        return $this->_entity->setState($data);
    }

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity.
     */
    public function getField($name)
    {
        return $this->_entity->getField($name);
    }

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity.
     */
    public function getMethods()
    {
        return $this->_caller->getApplicableMethods($this->_entity->getClass());
    }

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity.
     * Convenience method.
     */
    public function getMethod($methodName)
    {
        $methods = $this->getMethods();
        return $methods[$methodName];
    }

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity.
     * Convenience method.
     */
    public function hasMethod($methodName)
    {
        $methods = $this->getMethods();
        return isset($methods[$methodName]);
    }

    public function __call($methodName, array $arguments = array())
    {
        return $this->_caller->call($this->_entity, $methodName, $arguments);
    }

    public function getIterator()
    {
        return $this->_entity->getIterator();
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
     * Proxies to the wrapped entity.
     */
    public function getFacet($type)
    {
        return $this->_entity->getFacet($type);
    }

    /**
     * {@inheritdoc}
     * Proxies to the wrapped entity.
     */
    public function getFacets($type)
    {
        return $this->_entity->getFacets($type);
    }

}
