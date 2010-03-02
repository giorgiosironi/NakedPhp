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
        $this->_reflectorMock->expects($this->once())
                             ->method('getIdentifierForAssociation')
                             ->will($this->returnValue('myField'));
        $this->_reflectorMock->expects($this->once())
                             ->method('getReturnType')
                             ->will($this->returnValue('integer'));
        $dummySpec = new NakedObjectSpecificationStub();
        $this->_loaderMock->expects($this->once())
                          ->method('loadSpecification')
                          ->with('integer')
                          ->will($this->returnValue($dummySpec));

        $method = $this->_getDummyReflectionMethod();
        $association = $this->_factory->createAssociation($method);

        $this->assertEquals('myField', $association->getId());
        $this->assertSame($dummySpec, $association->getType());
    }
    
    public function testAssociationsTypeDefaultsToString()
    {
        $this->_reflectorMock->expects($this->once())
                             ->method('getReturnType')
                             ->will($this->returnValue(null));

        $this->_loaderMock->expects($this->once())
                          ->method('loadSpecification')
                          ->with('string');

        $method = $this->_getDummyReflectionMethod();
        $association = $this->_factory->createAssociation($method);
    }
    
    public function testCreatesAction()
    {
        $this->_reflectorMock->expects($this->once())
                             ->method('getIdentifierForAction')
                             ->will($this->returnValue('myMethod'));
        $this->_reflectorMock->expects($this->once())
                             ->method('getReturnType')
                             ->will($this->returnValue('string'));
        $this->_reflectorMock->expects($this->once())
                             ->method('getParameters')
                             ->will($this->returnValue(array(
                                'myParam' => array(
                                    'type' => 'integer'
                                )
                             )));
        $dummySpec = new NakedObjectSpecificationStub();
        $this->_loaderMock->expects($this->exactly(2))
                          ->method('loadSpecification')
                          ->will($this->returnValue($dummySpec));

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
        $this->_reflectorMock->expects($this->once())
                             ->method('getIdentifierForAction')
                             ->will($this->returnValue('myMethod'));
        $this->_reflectorMock->expects($this->once())
                             ->method('getReturnType')
                             ->will($this->returnValue(null));
        $this->_reflectorMock->expects($this->once())
                             ->method('getParameters')
                             ->will($this->returnValue(array(
                                'myParam' => array(
                                    'type' => null
                                )
                             )));
        $dummySpec = new NakedObjectSpecificationStub();
        $this->_loaderMock->expects($this->exactly(2))
                          ->method('loadSpecification')
                          ->with('string');

        $method = $this->_getDummyReflectionMethod();
        $action = $this->_factory->createAction($method);
    }
 
    private function _getDummyReflectionMethod()
    {
        return $this->getMock('ReflectionMethod', array(), array(), '', false);
    }
}
