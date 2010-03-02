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
use NakedPhp\Reflect\Introspector\PhpClassIntrospector;
use NakedPhp\Reflect\Introspector\PhpTypeIntrospector;

class PhpIntrospectorFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $_introspectors;

    public function setUp()
    {
        $specFactory = new DummySpecificationFactory();
        $secondSpecFactory = new DummySecondSpecificationFactory();
        $introspectorFactory = new PhpIntrospectorFactory(array(
            $specFactory,
            $secondSpecFactory
        ));
        $this->_introspectors = $introspectorFactory->getIntrospectors();
    }

    public function testCreatesOneIntrospectorForEverySpecification()
    {
        $this->assertEquals(4, count($this->_introspectors));
    }

    public function testCreatesPhpClassIntrospectorObjects()
    {
        $introspector = $this->_introspectors['My_Model_EntityA'];
        $this->assertTrue($introspector instanceof PhpClassIntrospector);
    }

    public function testCreatesPhpTypeIntrospectorObjects()
    {
        $introspector = $this->_introspectors['string'];
        $this->assertTrue($introspector instanceof PhpTypeIntrospector);
    }

    public function testPutsSpecificationInIntrospectorObjects()
    {
        $specification = $this->_introspectors['My_Model_EntityA']->getSpecification();
        $this->assertTrue($specification instanceof NakedObjectSpecification);
        $this->assertEquals('My_Model_EntityA', (string) $specification);

        $specification = $this->_introspectors['string']->getSpecification();
        $this->assertTrue($specification instanceof NakedObjectSpecification);
        $this->assertEquals('string', (string) $specification);
    }
}

class DummySpecificationFactory implements SpecificationFactory
{
    public function getSpecifications()
    {
        $serviceSpec = new PhpSpecification('My_Model_Service');
        $serviceSpec->markAsService();
        return array(
            'My_Model_EntityA' => new PhpSpecification('My_Model_EntityA'),
            'My_Model_EntityB' => new PhpSpecification('My_Model_EntityB'),
            'My_Model_Service' => $serviceSpec
        );
    }
}

class DummySecondSpecificationFactory implements SpecificationFactory
{
    public function getSpecifications()
    {
        return array(
            'string' => new PhpSpecification('string')
        );
    }
}
