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
use NakedPhp\MetaModel\NakedObjectFeatureType;
use NakedPhp\ProgModel\OneToOneAssociation;
use NakedPhp\ProgModel\PhpAction;
use NakedPhp\ProgModel\PhpSpecification;

/**
 * PhpIntrospector must accomplish:
 * - introspection of class
 * - introspection of associations
 * - introspection of action method
 * TODO: add indirection using a MetaModelFactory
 */
class PhpIntrospectorTest extends \PHPUnit_Framework_TestCase
{
    private $_specification;
    private $_facetProcessor;
    private $_introspector;
    private $_metaModelFactory;
    private $_createdAssociation;
    private $_createdAction;

    public function setUp()
    {
        $this->_specification = new PhpSpecification('NakedPhp\Reflect\DummyClass', null, null);
        $this->_facetProcessor = $this->getMock('NakedPhp\Reflect\FacetProcessor');
        $this->_metaModelFactory = $this->getMock('NakedPhp\Reflect\MetaModelFactory');
        $this->_createdAssociation = new OneToOneAssociation('dummy');
        $this->_createdAction = new PhpAction('dummy');
        $this->_metaModelFactory->expects($this->any())
                                ->method('createAssociation')
                                ->will($this->returnValue($this->_createdAssociation));
        $this->_metaModelFactory->expects($this->any())
                                ->method('createAction')
                                ->will($this->returnValue($this->_createdAction));
        $this->_introspector = new PhpIntrospector($this->_specification,
                                                   $this->_facetProcessor,
                                                   $this->_metaModelFactory);
    }

    public function testIntrospectsClass()
    {
        $this->_facetProcessor->expects($this->once())
                              ->method('processClass')
                              ->with($this->anything(), $this->anything(), $this->_specification, NakedObjectFeatureType::OBJECT);
        $this->_facetProcessor->expects($this->exactly(4))
                              ->method('processMethod')
                              ->with($this->anything(), $this->anything(), $this->anything(), $this->_specification, NakedObjectFeatureType::OBJECT);

        $this->_introspector->introspectClass();
    }

    public function testIntrospectsAssociations()
    {
        $rc = new \ReflectionClass('NakedPhp\Reflect\DummyClass');
        $methods = array($rc->getMethod('getFoo'), $rc->getMethod('getBar'));
        $this->_facetProcessor->expects($this->once())
                              ->method('removePropertyAccessors')
                              ->will($this->returnValue($methods));

        $this->_facetProcessor->expects($this->exactly(2))
                              ->method('processClass')
                              ->with($this->anything(), $this->anything(), $this->_createdAssociation, NakedObjectFeatureType::PROPERTY);

        $this->_facetProcessor->expects($this->exactly(2))
                              ->method('processMethod')
                              ->with($this->anything(), $this->anything(), $this->anything(), $this->_createdAssociation, NakedObjectFeatureType::PROPERTY);

        $this->_introspector->introspectAssociations();

        $associations = $this->_specification->getAssociations();
        $this->assertEquals(2, count($associations));
        $this->assertTrue($associations['foo'] instanceof OneToOneAssociation);
        $this->assertTrue($associations['bar'] instanceof OneToOneAssociation);
    }

    public function testIntrospectsObjectActions()
    {
        $this->_facetProcessor->expects($this->exactly(4))
                              ->method('recognizes')
                              ->will($this->returnCallback(array($this, 'recognizes')));

        $this->_facetProcessor->expects($this->exactly(2))
                              ->method('processClass')
                              ->with($this->anything(), $this->anything(), $this->_createdAction, NakedObjectFeatureType::ACTION);

        $this->_facetProcessor->expects($this->exactly(8))
                              ->method('processMethod')
                              ->with($this->anything(), $this->anything(), $this->anything(), $this->_createdAction, NakedObjectFeatureType::ACTION);

        $this->_introspector->introspectActions();

        $actions = $this->_specification->getObjectActions();
        $this->assertEquals(2, count($actions));
        $this->assertTrue($actions['anotherMethod'] instanceof PhpAction);
        $this->assertTrue($actions['andStillAnotherMethod'] instanceof PhpAction);
    }

    public function recognizes(\ReflectionMethod $method)
    {
        $name = $method->getName();
        if (strstr($name, 'get') or strstr($name, 'set')) {
            return true;
        }
        return false;
    }
}

class DummyClass
{
    public function getFoo() {}
    public function getBar() {}
    public function anotherMethod() {}
    public function andStillAnotherMethod() {}
}
