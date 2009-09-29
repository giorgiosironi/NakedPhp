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
use NakedPhp\Metadata\NakedService;
use NakedPhp\Metadata\NakedServiceClass;
use NakedPhp\Metadata\NakedMethod;
use NakedPhp\Metadata\NakedParam;
use NakedPhp\Stubs\User;

class MethodMergerTest extends \PHPUnit_Framework_TestCase
{
    private $_providerMock;
    private $_factoryMock;
    private $_methodMerger;
    private $_serviceClass;
    private $_called;
    private $_object;

    public function setUp()
    {
        $this->_providerMock = $this->getMock('NakedPhp\Service\ServiceProvider', array('getServiceClasses', 'getService'));
        $this->_factoryMock = $this->getMock('NakedPhp\Service\NakedFactory', array('create'));
        $this->_methodMerger = new MethodMerger($this->_providerMock, $this->_factoryMock);
    }

    private function _setAvailableServiceClasses($services)
    {
        $this->_providerMock->expects($this->any())
                            ->method('getServiceClasses')
                            ->will($this->returnValue($services));
    }

    public function testCallsAMethodOfTheObjectClass()
    {
        $mock = $this->getMock('NakedPhp\Stubs\User', array('sendMessage'));
        $mock->expects($this->once())
             ->method('sendMessage')
             ->with('Title', 'text...');
        $this->_methodMerger->call(new NakedEntity($mock), 'sendMessage', array('Title', 'text...'));
    }

    public function testListsMethodOfTheObjectClass()
    {
        $this->_setAvailableServiceClasses(array());
        $class = new NakedEntityClass('NakedPhp\Stubs\User', array('doSomething' => new NakedMethod('doSomething')));
        $methods = $this->_methodMerger->getApplicableMethods($class);
        $this->assertTrue(isset($methods['doSomething']));
    }

    public function testListsMethodOfTheServiceClassesWhichTakesEntityAsAnArgument()
    {
        $this->_makeProcessMethodAvailable();
        $class = new NakedEntityClass('NakedPhp\Stubs\User', array());
        $methods = $this->_methodMerger->getApplicableMethods($class);
        $this->assertTrue(isset($methods['process']));
    }

    public function testDoesNotListEntityAsAnArgumentOfAServiceMethod()
    {
        $this->_makeProcessMethodAvailable();
        $class = new NakedEntityClass('NakedPhp\Stubs\User', array());
        $methods = $this->_methodMerger->getApplicableMethods($class);
        $params = $methods['process']->getParams();
        $this->assertEquals(0, count($params));
    }

    public function testCallsAServiceMethodAsIfItWereOnTheEntityClass()
    {
        $this->_makeProcessMethodAvailable();
        $service = new NakedService($this, $this->_serviceClass);
        $this->_providerMock->expects($this->once())
                            ->method('getService')
                            ->with('theService')
                            ->will($this->returnValue($service));
        $class = new NakedEntityClass('NakedPhp\Stubs\User', array());
        $this->_object = new \NakedPhp\Stubs\User;
        $user = new NakedEntity($this->_object, $class);
        $this->_called = false;
        $methods = $this->_methodMerger->call($user, 'process');
        $this->assertTrue($this->_called);
    }

    public function process($argument)
    {
        $this->assertEquals($this->_object, $argument);
        $this->_called = true;
    }

    private function _makeProcessMethodAvailable()
    {
        $this->_serviceClass = new NakedServiceClass('', array(
            'process' => new NakedMethod('process', array(
                'objectToProcess' => new NakedParam('NakedPhp\Stubs\User', 'objectToProcess')
            ))
        ));
        $this->_setAvailableServiceClasses(array(
            'theService' => $this->_serviceClass
        ));
    }

    public function testWrapsObjectResultUsingNakedFactory()
    {
        $expectedResult = new NakedEntity(new \stdClass);
        $this->_factoryMock->expects($this->any())
                           ->method('create')
                           ->will($this->returnValue($expectedResult));
        $mock = $this->getMock('NakedPhp\Stubs\User', array('getStatus'));
        $mock->expects($this->once())
             ->method('getStatus')
             ->will($this->returnValue(new \stdClass));
        $result = $this->_methodMerger->call(new NakedEntity($mock), 'getStatus');
        $this->assertSame($expectedResult, $result);
    }

    public function testDoesNotWrapScalarResult()
    {
        $mock = $this->getMock('NakedPhp\Stubs\User', array('getStatus'));
        $mock->expects($this->once())
             ->method('getStatus')
             ->will($this->returnValue('foo'));
        $result = $this->_methodMerger->call(new NakedEntity($mock), 'getStatus');
        $this->assertSame('foo', $result);
    }

    public function testRecognizeParametersNeed()
    {
        $this->_setAvailableServiceClasses(array());
        $no = $this->_createFakeEntity();
        $this->assertTrue($this->_methodMerger->needArguments($no, 'doSomething'));
    }

    public function testRecognizeParametersNeedOnServiceMethods()
    {
        $this->_makeProcessMethodAvailable();
        $class = new NakedEntityClass('NakedPhp\Stubs\User');
        $no = new NakedEntity(new \NakedPhp\Stubs\User, $class);
        $this->assertFalse($this->_methodMerger->needArguments($no, 'process'));
    }

    public function testRecognizeMethodsWithoutParameters()
    {
        $this->_setAvailableServiceClasses(array());
        $no = $this->_createFakeEntity();
        $this->assertFalse($this->_methodMerger->needArguments($no, 'doAnything'));
    }

    protected function _createFakeEntity()
    {
        $methods = array(
            'doSomething' => new NakedMethod('', array(null, null)),
            'doAnything' => new NakedMethod('')
        );
        return new NakedEntity(new \stdClass, new NakedEntityClass('NakedPhp\Stubs\User', $methods));
    }

    public function testExtractMetadataForAMethod()
    {
        $this->_setAvailableServiceClasses(array());
        $methods = array(
            'doSomething' => $expectedMethod = new NakedMethod(''),
        );
        $no = new NakedEntity(new \stdClass, new NakedEntityClass('NakedPhp\Stubs\User', $methods));

        $this->assertSame($expectedMethod,
                          $this->_methodMerger->getMethod($no, 'doSomething'));
    }

    public function testExtractBuiltMetadataForAServiceMethod()
    {
        $this->_serviceClass = new NakedServiceClass('', array(
            'block' => new NakedMethod('block', array(
                'user' => new NakedParam('NakedPhp\Stubs\User', 'user'),
                'days' => $days = new NakedParam('integer', 'days')
            ))
        ));
        $this->_setAvailableServiceClasses(array(
            'theService' => $this->_serviceClass
        ));

        $class = new NakedEntityClass('NakedPhp\Stubs\User');
        $no = new NakedEntity(new \NakedPhp\Stubs\User, $class);

        $method = $this->_methodMerger->getMethod($no, 'block');
        $this->assertEquals(array('days' => $days), $method->getParams());
    }
}
