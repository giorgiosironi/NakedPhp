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

    private $_folder;
    private $_prefix;

    /**
     * @param string $folder    folder containing classes' source files
     * @param string $prefix    prefix of contained classes 
     * <code>
     * $factory = new \NakedPhp\Factory(APP_PATH . 'models/', 'Example_Model_');
     */
    public function __construct($folder, $prefix)
    {
        $this->_folder = $folder;
        $this->_prefix = $prefix;
        $this->_reflectFactory = new \NakedPhp\Reflect\ReflectFactory();
    }

    protected function _getSessionBridge()
    {
        if (!isset($this->_sessionBridge)) {
            $this->_sessionBridge = new \Zend_Session_Namespace('NakedPhp');
        }
        return $this->_sessionBridge;
    }

    public function getUnwrappedContainer()
    {
        if (!isset($this->_getSessionBridge()->unwrappedContainer)) {
            $this->_getSessionBridge()->unwrappedContainer = new Mvc\EntityContainer\UnwrappedContainer(array());
        }
        return $this->_getSessionBridge()->unwrappedContainer;
    }

    public function getBareWrappingIterator()
    {
        return new Mvc\EntityContainer\BareWrappingIterator($this->getUnwrappedContainer(),
                                                            $this->getNakedFactory());
    }

    public function getContextContainer()
    {
        if (!isset($this->_getSessionBridge()->contextContainer)) {
            $this->_getSessionBridge()->contextContainer = new Mvc\ContextContainer();
        }
        return $this->_getSessionBridge()->contextContainer;
    }

    public function getServiceIterator()
    {
        return new Service\ServiceIterator($this->getServiceProvider());
    }

    /**
     * TODO: make private
     */
    public function getMethodMerger()
    {
        $serviceProvider = $this->getServiceProvider();
        return new Service\MethodMerger($serviceProvider, $this->getNakedFactory());
    }

    public function getServiceProvider()
    {
        $reflector = $this->_reflectFactory->createServiceReflector();
        $serviceDiscoverer = new Service\FilesystemServiceDiscoverer($reflector, __DIR__ . '/../../example/application/models/', 'Example_Model_');
        return new Service\Provider\EmptyConstructorsProvider($serviceDiscoverer, $this->_reflectFactory->createServiceReflector());
    }

    /**
     * TODO: make private if possible
     * @return MetaModel\NakedFactory
     */
    public function getNakedFactory()
    {
        $loader = $this->_reflectFactory->createSpecificationLoader($this->_folder, $this->_prefix);
        $loader->init();
        return new Service\NakedFactory($loader);
    }

    public function getMethodFormBuilder()
    {
        return new Form\MethodFormBuilder();
    }

    public function getFieldsFormBuilder()
    {
        return new Form\FieldsFormBuilder($this->getMethodMerger());
    }

    public function getStateManager()
    {
        return new Form\StateManager($this->getBareWrappingIterator());
    }

    /**
     * @return NakedObjectMethodDecorator
     */
    public function createCompleteEntity(ProgModel\NakedBareObject $entity)
    {
        return new ProgModel\NakedObjectMethodDecorator($entity, $this->getMethodMerger());
    }

    /**
     * TODO: unify with @see createCompleteEntity
     * @return NakedObjectMethodDecorator
     */
    public function createCompleteService(ProgModel\NakedBareObject $entity)
    {
        return new ProgModel\NakedObjectMethodDecorator($entity, $this->getMethodMerger());
    }

    public function getPersistenceStorage()
    {
        require_once __DIR__ . '/../../example/bin/cli-config.php';
        return new Storage\Doctrine($em);
    }
}
