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
use NakedPhp\Metadata\NakedBareEntity;
use NakedPhp\Metadata\NakedEntityClass;
use NakedPhp\Metadata\NakedBareService;
use NakedPhp\Metadata\NakedServiceClass;
use NakedPhp\Metadata\NakedMethod;
use NakedPhp\Metadata\NakedParam;
use NakedPhp\Stubs\User;

class MethodMergerTest extends \PHPUnit_Framework_TestCase
{
    private $_providerMock;
    private $_methodMerger;
    private $_serviceClass;
    private $_callbackCalled;
    private $_object;

    public function setUp()
    {
        $this->_providerMock = $this->getMock('NakedPhp\Service\ServiceProvider', array('getServiceClasses', 'getService'));
        $this->_methodMerger = new MethodMerger($this->_providerMock);

        $this->_callbackCalled = false;
    }
    
    private function _wrapEntity($entityObject, $method = null)
    {
        if (is_null($method)) {
            $methods = array();
        } else if (is_string($method)) {
            $methods[$method] = new NakedMethod($method);
        } else {
            $methodName = (string) $method;
            $methods[$methodName] = $method;
        }
        return new NakedBareEntity($entityObject, new NakedEntityClass('', $methods));
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
             ->with('Title', 'text...')
             ->will($this->returnValue('dummy'));
        $entity = $this->_wrapEntity($mock, new NakedMethod('sendMessage', array(
            'title' => new NakedParam('string', 'title'),
            'text' => new NakedParam('string', 'text')
        )));

        $result = $this->_methodMerger->call($entity, 'sendMessage', array('Title', 'text...'));

        $this->assertEquals('dummy', $result);
    }

    public function testCallsAHiddenMethodOfTheObjectClass()
    {
        $class = new NakedEntityClass('NakedPhp\Stubs\User', array(), array(), array('iAmHidden' => new NakedMethod('iAmHidden', array('first' => new NakedParam('string', 'first')))));
        $mock = $this->getMock('NakedPhp\Stubs\User', array('iAmHidden'));
        $mock->expects($this->once())
             ->method('iAmHidden')
             ->with('foo');
        $entity = new NakedBareEntity($mock, $class);
        $this->_methodMerger->call($entity, 'iAmHidden', array('foo'));
    }

    public function testListsMethodOfTheObjectClass()
    {
        $this->_setAvailableServiceClasses(array());
        $class = new NakedEntityClass('NakedPhp\Stubs\User', array('doSomething' => new NakedMethod('doSomething')));
        $methods = $this->_methodMerger->getApplicableMethods($class);
        $this->assertTrue(isset($methods['doSomething']));
        $this->assertTrue($this->_methodMerger->hasMethod($class, 'doSomething'));
        $this->assertFalse($this->_methodMerger->hasMethod($class, 'doSomethingWhichDoesNotExist'));
    }

    public function testDoesNotListHiddenMethods()
    {
        $this->_setAvailableServiceClasses(array());
        $class = new NakedEntityClass('NakedPhp\Stubs\User', array(), array(), array('iAmHidden' => new NakedMethod('iAmHidden')));
        $methods = $this->_methodMerger->getApplicableMethods($class);
        $this->assertEquals(array(), $methods);
    }

    public function testFindOutIfAnHiddenMethodExists()
    {
        $this->_setAvailableServiceClasses(array());
        $class = new NakedEntityClass('NakedPhp\Stubs\User', array(), array(), array('iAmHidden' => new NakedMethod('iAmHidden')));
        $this->assertTrue($this->_methodMerger->hasHiddenMethod($class, 'iAmHidden'));
    }

    public function testCallsAServiceMethodAsIfItWereAnHiddenOneOnTheEntityClass()
    {
        $this->markTestIncomplete();
    }

    public function testListsMethodOfTheServiceClassesWhichTakesEntityAsAnArgument()
    {
        $this->_makeProcessMethodAvailable();
        $class = $this->_getEmptyEntityClass();
        $methods = $this->_methodMerger->getApplicableMethods($class);
        $this->assertTrue(isset($methods['process']));
    }

    public function testDoesNotListEntityAsAnArgumentOfAServiceMethod()
    {
        $this->_makeProcessMethodAvailable();
        $class = $this->_getEmptyEntityClass();
        $methods = $this->_methodMerger->getApplicableMethods($class);
        $params = $methods['process']->getParams();
        $this->assertEquals(0, count($params));
    }

    public function testCallsAServiceMethodAsIfItWereOnTheEntityClass()
    {
        $this->_makeProcessMethodAvailable();
        $service = new NakedBareService($this, $this->_serviceClass);
        $this->_providerMock->expects($this->once())
                            ->method('getService')
                            ->with('theService')
                            ->will($this->returnValue($service));
        $class = $this->_getEmptyEntityClass();
        $this->_object = new \NakedPhp\Stubs\User;
        $user = new NakedBareEntity($this->_object, $class);
        $methods = $this->_methodMerger->call($user, 'process');
        $this->assertTrue($this->_callbackCalled);
    }

    public function process($argument)
    {
        $this->assertEquals($this->_object, $argument);
        $this->_callbackCalled = true;
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

    public function testCallsAEntityMethodAutomaticallyInjectingServices()
    {
        $this->_setAvailableServiceClasses(array(
            'NakedPhp\Stubs\UserFactory' => new NakedServiceClass('NakedPhp\Stubs\UserFactory')
        ));
        $service = new \NakedPhp\Stubs\UserFactory();
        $this->_providerMock->expects($this->once())
                            ->method('getService')
                            ->with('NakedPhp\Stubs\UserFactory')
                            ->will($this->returnValue($service));

        $class = $this->_getEntityClassWithCreateNewMethod();
        $no = new NakedBareEntity($this, $class);

        $this->_methodMerger->call($no, 'createNew', array('John Doe'));
        $this->assertTrue($this->_callbackCalled);
    }

    public function createNew(\NakedPhp\Stubs\UserFactory $factory, $name)
    {
        $this->assertEquals('John Doe', $name);
        $this->_callbackCalled = true;
    }

    private function _getEntityClassWithCreateNewMethod()
    {
        return new NakedEntityClass('NakedPhp\Stubs\User', array(
            'createNew' => new NakedMethod('createNew', array(
                'userFactory' => new NakedParam('NakedPhp\Stubs\UserFactory', 'userFactory'),
                'name' => $name = new NakedParam('string', 'name')
            ))
        ));
    }

    private function _getEmptyEntityClass()
    {
        return  new NakedEntityClass('NakedPhp\Stubs\User', array());
    }

    protected function _createFakeEntity()
    {
        $methods = array(
            'doSomething' => new NakedMethod('', array(
                'param1' => new NakedParam('integer', 'param1'),
                'param2' => new NakedParam('string', 'param2')
            )),
            'doAnything' => new NakedMethod('')
        );
        return new NakedBareEntity(new \stdClass, new NakedEntityClass('NakedPhp\Stubs\User', $methods));
    }

    public function testExtractsMetadataForAMethod()
    {
        $this->_setAvailableServiceClasses(array());
        $methods = array(
            'doSomething' => $expectedMethod = new NakedMethod(''),
        );
        $class = new NakedEntityClass('NakedPhp\Stubs\User', $methods);

        $this->assertTrue($this->_methodMerger->hasMethod($class, 'doSomething'));
        $this->assertSame($expectedMethod,
                          $this->_methodMerger->getMethod($class, 'doSomething'));
    }

    public function testExtractsMetadataForAHiddenMethod()
    {
        $this->_setAvailableServiceClasses(array());
        $methods = array(
            'doSomething' => $expectedMethod = new NakedMethod(''),
        );
        $class = new NakedEntityClass('NakedPhp\Stubs\User', array(), array(), $methods);

        $this->assertTrue($this->_methodMerger->hasMethod($class, 'doSomething'));
        $this->assertSame($expectedMethod,
                          $this->_methodMerger->getMethod($class, 'doSomething'));
    }

    public function testExtractsBuiltMetadataForAServiceMethod()
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

        $class = $this->_getEmptyEntityClass();
        $this->assertTrue($this->_methodMerger->hasMethod($class, 'block'));
        $method = $this->_methodMerger->getMethod($class, 'block');
        $this->assertEquals(array('days' => $days), $method->getParams());
    }

    public function testExtractsBuiltMetadataForAEntityMethodWhichRequireAService()
    {
        $this->_setAvailableServiceClasses(array(
            'NakedPhp\Stubs\UserFactory' => new NakedServiceClass('NakedPhp\Stubs\UserFactory')
        ));

        $class = $this->_getEntityClassWithCreateNewMethod();

        $this->assertTrue($this->_methodMerger->hasMethod($class, 'createNew'));
        $method = $this->_methodMerger->getMethod($class, 'createNew');
        $this->assertEquals(array('name' => new NakedParam('string', 'name')), $method->getParams());
    }

    public function testSupportsMetadataBuildingWhenMoreThanOneParameterIsAService()
    {
        $this->markTestIncomplete();
    }
}
