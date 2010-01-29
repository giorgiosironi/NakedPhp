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

/**
 * TODO: maybe is is possible to inherit some behavior from AbstractNakedObjectTest
 */
class NakedObjectMethodDecoratorTest extends \NakedPhp\Test\TestCase
{
    protected $_object;
    protected $_delegation;

    public function setUp()
    {
        $original = $this->_getBareEntityMock();
        $this->_object = new NakedObjectMethodDecorator($original);
        $this->_delegation = new Delegation($this, $original);
    }

    public function testDelegatesToTheInnerEntityForClassMetadata()
    {
        $class = new NakedObjectSpecificationStub();
        $this->_delegation->getterIs('getSpecification', $class);

        $this->assertSame($class, $this->_object->getSpecification());
    }

    public function testDelegatesToTheInnerEntityForClassType()
    {
        $this->_delegation->getterIs('isService', 'aBoolean');
        $this->assertSame('aBoolean', $this->_object->isService());
    }

    public function testDelegatesToTheInnerEntityForStringRepresentation()
    {
        $this->_delegation->getterIs('__toString', 'STUBBED');

        $this->assertSame('STUBBED', (string) $this->_object);
    }

    public function testDelegatesToTheInnerEntityForUnwrapping()
    {
        $this->_delegation->getterIs('getObject', $entity = new \stdClass);

        $this->assertSame($entity, $this->_object->getObject());
    }

    public function testDelegatesToTheInnerEntityForObtainingFieldMetadata()
    {
        $this->_delegation->getterIs('getField', 'STUBBED');

        $this->assertSame('STUBBED', $this->_object->getField('foo'));
    }

    public function testDelegatesToTheInnerEntityForObtainingState()
    {
        $state = array('nickname' => 'dummy');
        $this->_delegation->getterIs('getState', $state);

        $this->assertEquals($state, $this->_object->getState());
    }

    public function testDelegatesToTheInnerEntityForSettingTheState()
    {
        $data = array('nickname' => 'dummy');
        $this->_delegation->setterIs('setState', $data);

        $this->_object->setState($data);
    }

    public function testDelegatesToTheInnerEntityForFacetHolding()
    {
        $this->_delegation->getterIs('getFacet', 'foo');
        $this->_delegation->getterIs('getFacets', array('foo', 'bar'));

        $this->assertEquals('foo', $this->_object->getFacet('Dummy'));
        $this->assertEquals(array('foo', 'bar'), $this->_object->getFacets('Dummy'));
    }

    public function testIsTraversable()
    {
        $this->assertTrue($this->_object instanceof \Traversable);
    }

    public function testIsTraversableDelegatingToTheInnerEntityIterator()
    {
        $this->_delegation->getterIs('getIterator', 'dummy');

        $this->assertEquals('dummy', $this->_object->getIterator());
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
