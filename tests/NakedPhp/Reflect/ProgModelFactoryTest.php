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

class ProgModelFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $_reflectorMock;
    private $_factory;

    public function setUp()
    {
        $this->_reflectorMock = $this->getMock('NakedPhp\Reflect\MethodsReflector');
        $this->_factory = new ProgModelFactory($this->_reflectorMock);
    }

    public function testCreatesAssociation()
    {
        $this->_reflectorMock->expects($this->once())
                             ->method('getIdentifierForAssociation')
                             ->will($this->returnValue('myField'));
        $this->_reflectorMock->expects($this->once())
                             ->method('getReturnType')
                             ->will($this->returnValue('integer'));

        $method = $this->_getDummyReflectionMethod();
        $association = $this->_factory->createAssociation($method);

        $this->assertEquals('myField', $association->getId());
        $this->assertEquals('integer', $association->getType());
    }
    
    public function testAssociationsTypeDefaultsToString()
    {
        $this->_reflectorMock->expects($this->once())
                             ->method('getReturnType')
                             ->will($this->returnValue(null));

        $method = $this->_getDummyReflectionMethod();
        $association = $this->_factory->createAssociation($method);

        $this->assertEquals('string', $association->getType());
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

        $method = $this->_getDummyReflectionMethod();
        $action = $this->_factory->createAction($method);

        $this->assertEquals('myMethod', $action->getId());
        $this->assertEquals('string', $action->getReturnType());
        $params = $action->getParameters();
        $param = $params['myParam'];
        $this->assertEquals('myParam', $param->getId());
        $this->assertEquals('integer', $param->getType());
    }
    
    private function _getDummyReflectionMethod()
    {
        return $this->getMock('ReflectionMethod', array(), array(), '', false);
    }
}
