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
 * @package    NakedPhp_Metadata
 */

namespace NakedPhp\Metadata;
use NakedPhp\Service\MethodMerger;
use NakedPhp\Stubs\DummyFacet;
use NakedPhp\Test\Delegation;

class NakedCompleteServiceTest extends \NakedPhp\Test\TestCase
{
    private $_original;
    private $_completeObject;
    private $_delegation;

    public function setUp()
    {
        $this->_original = $this->getMock('NakedPhp\Metadata\NakedBareService');
        $this->_completeObject = new NakedCompleteService($this->_original);
        $this->_delegation = new Delegation($this, $this->_original);
    }

    public function testDelegatesToWrappedServiceForClassMetadata()
    {
        $class = new NakedServiceClass();
        $this->_delegation->getterIs('getClass', $class);
        $this->_delegation->getterIs('getClassName', 'FooClass');

        $this->assertSame($class, $this->_completeObject->getClass());
        $this->assertEquals('FooClass', $this->_completeObject->getClassName());
    }

    public function testDelegatesToWrappedServiceForStringRepresentation()
    {
        $this->_delegation->getterIs('__toString', 'dummy');

        $this->assertEquals('dummy', (string) $this->_completeObject);
    }

    public function testDelegatesToWrappedServiceForUnwrapping()
    {
        $this->_delegation->getterIs('getObject', $service = new \stdClass);

        $this->assertEquals($service, $this->_completeObject->getObject());
    }

    public function testDelegatesToWrappedServiceForMethodsMetadata()
    {
        $this->_delegation->getterIs('getMethods', array('key' => 'doSomething'));
        $this->_delegation->getterIs('getMethod', 'doSomething');
        $this->_delegation->getterIs('hasMethod', true);

        $this->assertEquals(array('key' => 'doSomething'), $this->_completeObject->getMethods());
        $this->assertEquals('doSomething', $this->_completeObject->getMethod('key'));
        $this->assertTrue($this->_completeObject->hasMethod('key'));
    }

    public function testDelegatesToWrappedServiceForFacetHolding()
    {
        $this->_delegation->getterIs('getFacet', new DummyFacet());
        $this->_delegation->getterIs('getFacets', array('foo', 'bar'));

        $this->assertTrue($this->_completeObject->getFacet('DummyFacet') instanceof DummyFacet);
        $this->assertEquals(array('foo', 'bar'), $this->_completeObject->getFacets('DummyFacet'));
    }

    public function testDelegatesMethodCallingToMethodCaller()
    {
        $bareNo = $this->getMock('NakedPhp\Metadata\NakedBareService');
        $merger = $this->getMock('NakedPhp\Service\MethodMerger');
        $merger->expects($this->once())
             ->method('call')
             ->with($bareNo, 'methodName', array('foo', 'bar'))
             ->will($this->returnValue('dummy'));
        $no = new NakedCompleteService($bareNo, $merger);

        $result = $no->__call('methodName', array('foo', 'bar'));

        $this->assertSame('dummy', $result);
    }
}
