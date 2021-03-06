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
 * @package    NakedPhp_ProgModel
 */

namespace NakedPhp\ProgModel;
use NakedPhp\Stubs\NakedObjectStub;
use NakedPhp\Stubs\NakedObjectSpecificationStub;
use NakedPhp\Test\Delegation;

/**
 * TODO: maybe is is possible to inherit some behavior from AbstractNakedObjectTest
 */
class NakedObjectMethodDecoratorTest extends \NakedPhp\Test\TestCase
{
    private $_original;
    private $_object;
    private $_delegation;

    public function setUp()
    {
        $this->_original = $this->_getBareObjectMock();
        $this->_object = new NakedObjectMethodDecorator($this->_original);
        $this->_delegation = new Delegation($this, $this->_original);
    }
    
    public function testGivesAccessToTheDecoratedInternalNakedObject()
    {
        $this->assertSame($this->_original,
                          $this->_object->getDecoratedObject());
    }

    public function testDelegatesToTheInnerEntityForTestingEquality()
    {
        $this->_delegation->getterIs('equals', true);
        $another = new NakedObjectMethodDecorator(new NakedObjectStub);

        $this->assertTrue($this->_object->equals($another));
    }

    public function testDelegatesToTheInnerEntityForClassMetaModel()
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

    public function testDelegatesToTheInnerEntityForObtainingFieldMetaModel()
    {
        $this->_delegation->getterIs('getAssociations', array('STUBBED'));

        $this->assertSame('STUBBED', $this->_object->getAssociation(0));
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
        $this->assertTrue($no->hasObjectAction('dummy'));
        $this->assertFalse($no->hasObjectAction('notExistentMethodName'));
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

    public function testCreatesNewInstance()
    {
        $spec = new NakedObjectSpecificationStub();
        $bareNo = $this->_getBareObjectMock();
        $newBareNo = $this->_getBareObjectMock();
        $bareNo->expects($this->once())
               ->method('createNewInstance')
               ->with('dummy', $spec)
               ->will($this->returnValue($newBareNo));
        $newBareNo->expects($this->any())
                  ->method('getObject')
                  ->will($this->returnValue('newDummy'));
        $merger = $this->_getMergerMock();
        $no = new NakedObjectMethodDecorator($bareNo, $merger);

        $new = $no->createNewInstance('dummy', $spec);

        $this->assertTrue($new instanceof NakedObjectMethodDecorator);
        $this->assertSame('newDummy', $new->getObject());
    }

    private function _getBareObjectMock()
    {
        return $this->getMock('NakedPhp\ProgModel\NakedBareObject');
    }

    private function _getMergerMock(array $methods = array('call'))
    {
        return $this->getMock('NakedPhp\Service\MethodMerger', $methods);
    }
}
