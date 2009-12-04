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

class NakedCompleteEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testDelegatesToTheInnerEntityForClassMetadata()
    {
        $no = new NakedBareEntity($this, $class = new NakedEntityClass('', array(), array('name')));
        $completeNo = new NakedCompleteEntity($no, null);
        $this->assertSame($class, $completeNo->getClass());
        $this->assertSame('OBJECT', (string) $completeNo);
    }

    public function testUnwrapsTheInnerBareEntity()
    {
        $no = new NakedBareEntity($this);
        $completeNo = new NakedCompleteEntity($no, null);
        $this->assertSame($no, $completeNo->getBareEntity());
    }

    public function testDelegatesToTheInnerEntityForObtainingState()
    {
        $state = array('nickname' => 'dummy');
        $no = $this->_getBareEntityMock();
        $no->expects($this->once())
           ->method('getState')
           ->will($this->returnValue($state));
        $completeNo = new NakedCompleteEntity($no, null);

        $this->assertEquals($state, $completeNo->getState());
    }

    public function testDelegatesToTheInnerEntityForSettingTheState()
    {
        $data = array('nickname' => 'dummy');
        $no = $this->_getBareEntityMock();
        $no->expects($this->once())
           ->method('setState')
           ->with($data);
        $completeNo = new NakedCompleteEntity($no, null);

        $completeNo->setState($data);
    }

    public function testDelegatesToTheMergerForObtainingApplicableMethods()
    {
        $class = new NakedEntityClass('DummyClass');
        $bareNo = new NakedBareEntity($this, $class);
        $merger = $this->_getMergerMock(array('getApplicableMethods'));
        $merger->expects($this->any())
             ->method('getApplicableMethods')
             ->with($class)
             ->will($this->returnValue(array('dummy' => 'DummyMethod')));
        $no = new NakedCompleteEntity($bareNo, $merger);

        $this->assertEquals(array('dummy' => 'DummyMethod'), $no->getMethods());
        $this->assertEquals('DummyMethod', $no->getMethod('dummy'));
        $this->assertTrue($no->hasMethod('dummy'));
        $this->assertFalse($no->hasMethod('notExistentMethodName'));
    }

    public function testDelegatesToTheMergerForCallingMethods()
    {
        $bareNo = new NakedBareEntity();
        $merger = $this->_getMergerMock();
        $merger->expects($this->once())
             ->method('call')
             ->with($bareNo, 'methodName', array('foo', 'bar'))
             ->will($this->returnValue('dummy'));
        $no = new NakedCompleteEntity($bareNo, $merger);

        $this->assertEquals('dummy', $no->__call('methodName', array('foo', 'bar')));
    }

    public function testDelegatesToTheMergerForSearchingTemplateMethods()
    {
        $class = new NakedEntityClass('DummyClass');
        $bareNo = new NakedBareEntity($this, $class);
        $merger = $this->_getMergerMock(array('hasHiddenMethod'));
        $merger->expects($this->any())
               ->method('hasHiddenMethod')
               ->with($class, 'dummyMethodName')
               ->will($this->returnValue(true));
        $no = new NakedCompleteEntity($bareNo, $merger);

        $this->assertTrue($no->hasHiddenMethod('dummyMethodName'));
    }

    public function testIsTraversable()
    {
        $no = new NakedBareEntity();
        $this->assertTrue($no instanceof \IteratorAggregate);
    }

    public function testIsTraversableDelegatingToTheInnerEntityIterator()
    {
        $iterator = 'dummy';
        $no = $this->_getBareEntityMock();
        $no->expects($this->once())
           ->method('getIterator')
           ->will($this->returnValue($iterator));
        $completeNo = new NakedBareEntity($no);

        $this->assertEquals('dummy', $no->getIterator());
    }

    private function _getBareEntityMock()
    {
        return $this->getMock('NakedPhp\Metadata\NakedBareEntity');
    }

    private function _getFactoryMock()
    {
        return $this->getMock('NakedPhp\Service\NakedFactory', array('create'));
    }
    
    private function _getMergerMock(array $methods = array('call'))
    {
        return $this->getMock('NakedPhp\Service\MethodMerger', $methods);
    }
}
