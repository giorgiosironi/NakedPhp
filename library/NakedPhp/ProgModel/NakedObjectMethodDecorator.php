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
use NakedPhp\Service\MethodCaller;

/**
 * Wraps a NakedBareObject object, providing automatic injection of services
 * as methods parameters (Decorator pattern).
 * Should not be serialized. Store the inner object instead (@see getObject()).
 */
class NakedObjectMethodDecorator extends AbstractNakedObjectDecorator
{
    protected $_caller;

    public function __construct(NakedBareObject $entity = null, MethodCaller $methodCaller = null)
    {
        $this->_entity = $entity;
        $this->_caller = $methodCaller;
    }

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity with the aid of the MethodCaller.
     */
    public function getObjectActions()
    {
        return $this->_caller->getApplicableMethods($this->_entity->getSpecification());
    }

    /**
     * {@inheritdoc}
     * Proxies to wrapped entity with the aid of the MethodCaller.
     */
    public function __call($methodName, array $arguments = array())
    {
        return $this->_caller->call($this->_entity, $methodName, $arguments);
    }
}
