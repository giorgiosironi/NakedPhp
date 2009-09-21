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

namespace NakedPhp;

class Factory
{
    /**
     * @var \Zend_Session_Namespace     used to store session-wide objects
     */
    private $_sessionBridge;

    public function __construct()
    {
        $this->_reflectFactory = new \NakedPhp\Reflect\ReflectFactory();
    }

    public function getEntityContainer()
    {
        $this->_sessionBridge = new \Zend_Session_Namespace('NakedPhp');
        if (!isset($this->_sessionBridge->entityContainer)) {
            $this->_sessionBridge->entityContainer = new Service\EntityContainer(array());
        }
        return $this->_sessionBridge->entityContainer;
    }

    public function getServiceIterator()
    {
        return new Service\ServiceIterator($this->getServiceProvider());
    }

    public function getMethodMerger()
    {
        $serviceProvider = $this->getServiceProvider();
        return new Service\MethodMerger($serviceProvider, $this->getNakedFactory());
    }

    public function getServiceProvider()
    {
        $reflector = $this->_reflectFactory->createServiceReflector();
        $serviceDiscoverer = new Service\FilesystemServiceDiscoverer($reflector, __DIR__ . '/../../example/application/models/', 'Example_Model_');
        return new Service\EmptyConstructorsServiceProvider($serviceDiscoverer, $this->_reflectFactory->createServiceReflector());
    }

    public function getNakedFactory()
    {
        return new Service\NakedFactory($this->_reflectFactory->createEntityReflector(),
                                        $this->_reflectFactory->createServiceReflector());
    }

    public function getMethodFormBuilder()
    {
        return new Form\MethodFormBuilder();
    }
}
