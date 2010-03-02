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
 * @package    NakedPhp_Reflect
 */

namespace NakedPhp\Reflect;
use NakedPhp\MetaModel\NakedObjectSpecification;
use NakedPhp\ProgModel\PhpSpecification;

class PhpSpecificationLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $_specLoader;

    public function setUp()
    {
        $introspectorFactory = new DummyIntrospectorFactory($this->_getIntrospector());
        $this->_specLoader = new PhpSpecificationLoader($introspectorFactory);
        $this->_specLoader->init();
    }

    public function testObtainsPhpSpecificationObjectsFromTheFactories()
    {
        $specification = $this->_specLoader->loadSpecification('My_Model_EntityA');
        $this->assertTrue($specification instanceof NakedObjectSpecification);
        $this->assertEquals('DummyClass', (string) $specification);
    }

    public function testDistinguishServices()
    {
        $serviceSpecs = $this->_specLoader->getServiceSpecifications();
        $this->assertEquals(array(), $serviceSpecs);
    }

    private function _getIntrospector()
    {
        $classes = 4;
        $introspector = $this->getMock('NakedPhp\Reflect\Introspector');
        $introspector->expects($this->exactly($classes))
                     ->method('getSpecification')
                     ->will($this->returnValue(new PhpSpecification('DummyClass')));
        $introspector->expects($this->exactly($classes))
                     ->method('introspectClass');
        $introspector->expects($this->exactly($classes))
                     ->method('introspectAssociations');
        $introspector->expects($this->exactly($classes))
                     ->method('introspectActions');
        return $introspector;
    }
}

class DummyIntrospectorFactory implements IntrospectorFactory
{
    protected $_introspector;

    public function __construct(Introspector $introspector)
    {
        $this->_introspector = $introspector;
    }

    public function getIntrospectors()
    {
        return array(
            'My_Model_EntityA' => $this->_introspector,
            'My_Model_EntityB' => $this->_introspector,
            'My_Model_Service' => $this->_introspector,
            'string' => $this->_introspector,
        );
    }
}
