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

namespace NakedPhp\Reflect\SpecificationFactory;
use NakedPhp\ProgModel\PhpSpecification;

class PhpClassesSpecificationFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $_specifications;

    public function setUp()
    {
        $classDiscoverer = new DummyClassDiscoverer();
        $specFactory = new PhpClassesSpecificationFactory($classDiscoverer);
        $this->_specifications = $specFactory->getSpecifications();
    }

    public function testCreatesPhpSpecificationObjectsGivenSomeClassNames()
    {
        $this->assertEquals(2, count($this->_specifications));
        $spec = current($this->_specifications);
        $this->assertEquals('My_Model_EntityA', $spec->getClassName());
    }

    public function testCreatesPhpSpecificationsWhoseAssociationsCanBeSet()
    {
        $spec = current($this->_specifications);
        $spec->initAssociations(array());
        $this->assertEquals(array(), $spec->getAssociations());
    }

    public function testCreatesPhpSpecificationsWhoseActionsCanBeSet()
    {
        $spec = current($this->_specifications);
        $spec->initObjectActions(array());
        $this->assertEquals(array(), $spec->getObjectActions());
    }
}

class DummyClassDiscoverer implements ClassDiscoverer
{
    public function getList()
    {
        return array(
            'My_Model_EntityA',
            'My_Model_EntityB'
        );
    }
}
