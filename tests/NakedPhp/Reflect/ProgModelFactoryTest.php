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
use NakedPhp\Stubs\NakedObjectSpecificationStub;

class ProgModelFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $_reflectorMock;
    private $_factory;
    private $_loaderMock;

    public function setUp()
    {
        $this->_reflectorMock = $this->getMock('NakedPhp\Reflect\MethodsReflector');
        $this->_factory = new ProgModelFactory($this->_reflectorMock);
        $this->_loaderMock = $this->getMock('NakedPhp\Reflect\SpecificationLoader');
        $this->_factory->initSpecificationLoader($this->_loaderMock);
    }

    public function testCreatesAssociation()
    {
        $this->_setReflectorMockExpectations('myField', 'integer');
        $dummySpec = new NakedObjectSpecificationStub();
        $this->_setLoaderMockExpectations('integer', $dummySpec, 1);

        $method = $this->_getDummyReflectionMethod();
        $association = $this->_factory->createAssociation($method);

        $this->assertEquals('myField', $association->getId());
        $this->assertSame($dummySpec, $association->getType());
    }
    
    public function testAssociationsTypeDefaultsToString()
    {
        $this->_setReflectorMockExpectations('myField', null);
        $this->_setLoaderMockExpectations('string', null, 1);

        $method = $this->_getDummyReflectionMethod();
        $association = $this->_factory->createAssociation($method);
    }
    
    public function testCreatesAction()
    {
        $params = array('myParam' => array('specificationName' => 'integer'));
        $this->_setReflectorMockExpectations('myMethod', 'string', $params);
        $dummySpec = new NakedObjectSpecificationStub();
        $this->_setLoaderMockExpectations(null, $dummySpec, 2);

        $method = $this->_getDummyReflectionMethod();
        $action = $this->_factory->createAction($method);

        $this->assertEquals('myMethod', $action->getId());
        $this->assertSame($dummySpec, $action->getReturnType());
        $params = $action->getParameters();
        $param = $params['myParam'];
        $this->assertEquals('myParam', $param->getId());
        $this->assertSame($dummySpec, $param->getType());
    }
    
    public function testCreatesActionTypesDefaultToString()
    {
        $params = array('myParam' => array('specificationName' => null));
        $this->_setReflectorMockExpectations('myMethod', null, $params);
        $this->_setLoaderMockExpectations('string', null, 2);

        $method = $this->_getDummyReflectionMethod();
        $action = $this->_factory->createAction($method);
    }

    /**
     * FIX: InvocationFacet should be managed here
     */
    public function testAddsInvocationFacetToAllActions()
    {
        $this->markTestIncomplete();
    }

    public function testAddsCollectionFacetsToActionWithANonScalarReturnType()
    {
        $returnType = array('specificationName' => 'array', 'typeOf' => 'stdClass');
        $this->_setReflectorMockExpectations('myMethod', $returnType);
        $dummySpec = new NakedObjectSpecificationStub('Dummy');
        $this->_setLoaderMockExpectations(null, $dummySpec);

        $method = $this->_getDummyReflectionMethod();
        $action = $this->_factory->createAction($method);

        $returnSpec = $action->getReturnType();
        // IMPORTANT: clones specifications before adding facets
        $this->assertNotSame($dummySpec, $returnSpec);

        $this->assertNotNull($returnSpec->getFacet('Collection'));
        $typeOfFacet = $returnSpec->getFacet('Collection\TypeOf');
        $this->assertEquals($dummySpec, $typeOfFacet->valueSpec());
    }

    /**
     * @param string $identifier    name of the method or association
     * @param string $returnType    null if not found
     * @param array  $parameters    keys are names, 
     *                              values are arrays('specificationName' => ...)
     */
    private function _setReflectorMockExpectations($identifier, $returnType, array $parameters = array())
    {
        $this->_reflectorMock->expects($this->any())
                             ->method('getIdentifierForAssociation')
                             ->will($this->returnValue($identifier));
        $this->_reflectorMock->expects($this->any())
                             ->method('getIdentifierForAction')
                             ->will($this->returnValue($identifier));
        if (!is_array($returnType)) {
            $returnType = array('specificationName' => $returnType);
        }
        $this->_reflectorMock->expects($this->once())
                             ->method('getReturnType')
                             ->will($this->returnValue($returnType));
        $this->_reflectorMock->expects($this->any())
                             ->method('getParameters')
                             ->will($this->returnValue($parameters));
    }

    private function _setLoaderMockExpectations($specName, NakedObjectSpecification $dummySpec = null, $times = null)
    {
        if ($times !== null) { 
            $matcher = $this->exactly($times);
        } else {
            $matcher = $this->any();
        }
        $expectation = $this->_loaderMock->expects($matcher);
        $expectation->method('loadSpecification')
                    ->will($this->returnValue($dummySpec));
        if ($specName !== null) {
            $expectation->with($specName);
        }
    }

    private function _getDummyReflectionMethod()
    {
        return $this->getMock('ReflectionMethod', array(), array(), '', false);
    }
}
