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
 * @package    NakedPhp_ProgModel
 */

namespace NakedPhp\ProgModel;
use NakedPhp\MetaModel\Facet;
use NakedPhp\MetaModel\NakedObject;

/**
 * Delegates all methods to the inner entity.
 */
abstract class AbstractNakedObjectDecorator implements NakedObject, \IteratorAggregate
{
    protected $_entity;

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity.
     */
    public function getSpecification()
    {
        return $this->_entity->getSpecification();
    }

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity.
     */
    public function getClassName()
    {
        return $this->_entity->getClassName();
    }

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity.
     */
    public function isService()
    {
        return $this->_entity->isService();
    }

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity.
     */
    public function getObject()
    {
        return $this->_entity->getObject();
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
    public function getAssociation($name)
    {
        return $this->_entity->getAssociation($name);
    }

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity.
     */
    public function getAssociations()
    {
        return $this->_entity->getAssociations();
    }

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity.
     */
    public function getObjectActions()
    {
        return $this->_entity->getObjectActions();
    }

    /**
     * {@inheritdoc}
     * Proxies to @see $this->getObjectActions().
     * Convenience method.
     */
    public function getObjectAction($methodName)
    {
        $methods = $this->getObjectActions();
        return $methods[$methodName];
    }

    /**
     * {@inheritdoc}
     * Proxies to @see $this->getObjectActions().
     * Convenience method.
     */
    public function hasObjectAction($methodName)
    {
        $methods = $this->getObjectActions();
        return isset($methods[$methodName]);
    }

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity.
     */
    public function __call($methodName, array $arguments = array())
    {
        return $this->_entity->_call($methodName, $arguments);
    }

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity.
     */
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
        throw new \Exception('Adding a Facet to an object is not allowed. Access the NakedObjectSpecification instance instead.');
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
