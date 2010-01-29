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
use NakedPhp\Stubs\NakedObjectSpecificationStub;
use NakedPhp\Test\Delegation;

class NakedObjectMethodDecoratorTest extends \NakedPhp\Test\TestCase
{
    private $_original;
    private $_completeObject;
    private $_delegation;

    public function setUp()
    {
        $this->_original = $this->_getBareEntityMock();
        $this->_completeObject = new NakedObjectMethodDecorator($this->_original);
        $this->_delegation = new Delegation($this, $this->_original);
    }

    public function testDelegatesToTheInnerEntityForClassMetadata()
    {
        $class = new NakedObjectSpecificationStub();
        $this->_delegation->getterIs('getSpecification', $class);

        $this->assertSame($class, $this->_completeObject->getSpecification());
    }

    public function testDelegatesToTheInnerEntityForStringRepresentation()
    {
        $this->_delegation->getterIs('__toString', 'STUBBED');

        $this->assertSame('STUBBED', (string) $this->_completeObject);
    }

    public function testDelegatesToTheInnerEntityForUnwrapping()
    {
        $this->_delegation->getterIs('getObject', $entity = new \stdClass);

        $this->assertSame($entity, $this->_completeObject->getObject());
    }

    public function testDelegatesToTheInnerEntityForObtainingFieldMetadata()
    {
        $this->_delegation->getterIs('getField', 'STUBBED');

        $this->assertSame('STUBBED', $this->_completeObject->getField('foo'));
    }


    public function testDelegatesToTheInnerEntityForObtainingState()
    {
        $state = array('nickname' => 'dummy');
        $this->_delegation->getterIs('getState', $state);

        $this->assertEquals($state, $this->_completeObject->getState());
    }

    public function testDelegatesToTheInnerEntityForSettingTheState()
    {
        $data = array('nickname' => 'dummy');
        $this->_delegation->setterIs('setState', $data);

        $this->_completeObject->setState($data);
    }

    public function testDelegatesToTheInnerEntityForFacetHolding()
    {
        $this->_delegation->getterIs('getFacet', 'foo');
        $this->_delegation->getterIs('getFacets', array('foo', 'bar'));

        $this->assertEquals('foo', $this->_completeObject->getFacet('Dummy'));
        $this->assertEquals(array('foo', 'bar'), $this->_completeObject->getFacets('Dummy'));
    }

    public function testIsTraversable()
    {
        $this->assertTrue($this->_completeObject instanceof \Traversable);
    }

    public function testIsTraversableDelegatingToTheInnerEntityIterator()
    {
        $this->_delegation->getterIs('getIterator', 'dummy');

        $this->assertEquals('dummy', $this->_completeObject->getIterator());
    }

    public function testDelegatesToTheMergerForObtainingApplicableMethods()
    {
        $class = new NakedObjectSpecificationStub('DummyClass');
        $bareNo = new NakedBareObject($this, $class);
        $merger = $this->_getMergerMock(array('getApplicableMethods'));
        $merger->expects($this->any())
             ->method('getApplicableMethods')
             ->with($class)
             ->will($this->returnValue(array('dummy' => 'DummyMethod')));
        $no = new NakedObjectMethodDecorator($bareNo, $merger);

        $this->assertEquals(array('dummy' => 'DummyMethod'), $no->getObjectActions());
        $this->assertEquals('DummyMethod', $no->getObjectAction('dummy'));
        $this->assertTrue($no->hasMethod('dummy'));
        $this->assertFalse($no->hasMethod('notExistentMethodName'));
    }

    public function testDelegatesToTheMergerForCallingMethods()
    {
        $bareNo = new NakedBareObject();
        $merger = $this->_getMergerMock();
        $merger->expects($this->once())
             ->method('call')
             ->with($bareNo, 'methodName', array('foo', 'bar'))
             ->will($this->returnValue('dummy'));
        $no = new NakedObjectMethodDecorator($bareNo, $merger);

        $this->assertEquals('dummy', $no->__call('methodName', array('foo', 'bar')));
    }

    private function _getBareEntityMock()
    {
        return $this->getMock('NakedPhp\Metadata\NakedBareObject');
    }

    private function _getMergerMock(array $methods = array('call'))
    {
        return $this->getMock('NakedPhp\Service\MethodMerger', $methods);
    }
}