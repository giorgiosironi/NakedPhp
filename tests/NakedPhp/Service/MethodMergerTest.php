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
 * @package    NakedPhp_Service
 */

namespace NakedPhp\Service;
use NakedPhp\Metadata\NakedObject;
use NakedPhp\Metadata\NakedEntity;
use NakedPhp\Metadata\NakedEntityClass;
use NakedPhp\Metadata\NakedMethod;

class MethodMergerTest extends \PHPUnit_Framework_TestCase
{
    private $_factoryMock;
    private $_methodMerger;

    public function setUp()
    {
        /*
        new \NakedPhp\Service\ServiceProvider();
        $mock = $this->getMock('NakedPhp\Service\ServiceProvider', array('getServiceClasses', 'getService'), array(), '', false, false, false);
        $mock->expects($this->any())
             ->method('getServiceClasses')
             ->will($this->returnValue(array()));
        */
        new \NakedPhp\Service\NakedFactory();
        $this->_factoryMock = $this->getMock('NakedPhp\Service\NakedFactory', array('create'), array(), '', false, false, false);
        $this->_methodMerger = new MethodMerger(null, $this->_factoryMock);
    }

    public function testCallsAMethodOfTheObjectClass()
    {
        new \NakedPhp\Stubs\User();
        $mock = $this->getMock('NakedPhp\Stubs\User', array('sendMessage'), array(), '', false, false, false);
        $mock->expects($this->once())
             ->method('sendMessage')
             ->with('Title', 'text...');
        $this->_methodMerger->call(new NakedObject($mock), 'sendMessage', array('Title', 'text...'));
    }

    public function testCallsAMethodOfTheObjectClassAlsoByPassingObject()
    {
        new \NakedPhp\Stubs\User();
        $mock = $this->getMock('NakedPhp\Stubs\User', array('sendMessage'), array(), '', false, false, false);
        $mock->expects($this->once())
             ->method('sendMessage')
             ->with('Title', 'text...');
        $this->_methodMerger->call(new NakedObject($mock), new NakedMethod('sendMessage'), array('Title', 'text...'));
    }

    public function testListsMethodOfTheObjectClass()
    {
        $class = new NakedEntityClass(array('doSomething' => new NakedMethod('doSomething')));
        $methods = $this->_methodMerger->getApplicableMethods($class);
        $this->assertEquals(array('doSomething'), array_keys($methods));
    }

    public function testWrapsObjectResultUsingNakedFactory()
    {
        $expectedResult = new NakedEntity(new \stdClass);
        $this->_factoryMock->expects($this->any())
                           ->method('create')
                           ->will($this->returnValue($expectedResult));
        new \NakedPhp\Stubs\User();
        $mock = $this->getMock('NakedPhp\Stubs\User', array('getStatus'), array(), '', false, false, false);
        $mock->expects($this->once())
             ->method('getStatus')
             ->will($this->returnValue(new \stdClass));
        $result = $this->_methodMerger->call(new NakedObject($mock), 'getStatus');
        $this->assertSame($expectedResult, $result);
    }

    public function testDoesNotWrapScalarResult()
    {
        new \NakedPhp\Stubs\User();
        $mock = $this->getMock('NakedPhp\Stubs\User', array('getStatus'), array(), '', false, false, false);
        $mock->expects($this->once())
             ->method('getStatus')
             ->will($this->returnValue('foo'));
        $result = $this->_methodMerger->call(new NakedObject($mock), 'getStatus');
        $this->assertSame('foo', $result);
    }

    public function testRecognizeParametersNeed()
    {
        $no = $this->_createFakeEntity();
        $this->assertTrue($this->_methodMerger->needArguments($no, 'doSomething'));
    }

    public function testRecognizeMethodsWithoutParameters()
    {
        $no = $this->_createFakeEntity();
        $this->assertFalse($this->_methodMerger->needArguments($no, 'doAnything'));
    }

    protected function _createFakeEntity()
    {
        $methods = array(
            'doSomething' => new NakedMethod('', array(null, null)),
            'doAnything' => new NakedMethod('')
        );
        return new NakedEntity(new \stdClass, new NakedEntityClass($methods));
    }

    public function testExtractMetadataForAMethod()
    {
        $methods = array(
            'doSomething' => $expectedMethod = new NakedMethod(''),
        );
        $no = new NakedEntity(new \stdClass, new NakedEntityClass($methods));

        $this->assertSame($expectedMethod,
                          $this->_methodMerger->getMethod($no, 'doSomething'));
    }
}
