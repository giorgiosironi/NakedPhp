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
    private $_serviceClassNames;

    /**
     * @param string $folder    folder containing classes' source files
     * @param string $prefix    prefix of contained classes 
     * <code>
     * $factory = new \NakedPhp\Factory(array(
     *      'folder' => APP_PATH . 'models/',
     *      'prefix' => 'Example_Model_',
     *      'serviceClassNames' => array('Example_Model_PlaceFactory')
     * ));
     */
    public function __construct(array $options)
    {
        $this->_folder            = $options['folder'];
        $this->_prefix            = $options['prefix'];
        $this->_serviceClassNames = $options['serviceClassNames'];
        $this->_reflectFactory    = new \NakedPhp\Reflect\ReflectFactory();
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
        $loader = $this->_getSpecificationLoader();
        $discoverer = new Service\ConfiguredServiceDiscoverer($loader, $this->_serviceClassNames);
        return new Service\Provider\EmptyConstructorsProvider($discoverer);
    }

    /**
     * TODO: make private if possible
     * @return MetaModel\NakedFactory
     */
    public function getNakedFactory()
    {
        return new Service\NakedFactory($this->_getSpecificationLoader());
    }

    protected function _getSpecificationLoader()
    {
        $specLoader = $this->_reflectFactory->createSpecificationLoader($this->_folder, $this->_prefix);
        $specLoader->init();
        return $specLoader;
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
