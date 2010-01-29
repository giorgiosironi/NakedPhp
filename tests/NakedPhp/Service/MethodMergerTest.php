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
use NakedPhp\Metadata\NakedBareObject;
use NakedPhp\Metadata\NakedObjectAction;
use NakedPhp\Metadata\NakedObjectActionParameter;
use NakedPhp\Metadata\Facet\Action\Invocation;
use NakedPhp\Stubs\NakedObjectSpecificationStub;
use NakedPhp\Stubs\User;

class MethodMergerTest extends \PHPUnit_Framework_TestCase
{
    private $_providerMock;
    private $_methodMerger;
    private $_serviceClass;
    private $_callbackCalled;
    private $_entity;

    public function setUp()
    {
        $this->_providerMock = $this->getMock('NakedPhp\Service\ServiceProvider', array('getServiceClasses', 'getService'));
        $this->_methodMerger = new MethodMerger($this->_providerMock);

        $this->_callbackCalled = false;
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

        $method = new NakedObjectAction('sendMessage', array(
            'title' => new NakedObjectActionParameter('string', 'title'),
            'text' => new NakedObjectActionParameter('string', 'text')
        ));
        $entity = new NakedBareObject($mock, new NakedObjectSpecificationStub('', array('sendMessage' => $method)));

        $result = $this->_methodMerger->call($entity, 'sendMessage', array('Title', 'text...'));

        $this->assertEquals('dummy', $result);
    }

    public function testListsMethodOfTheObjectClass()
    {
        $this->_setAvailableServiceClasses(array());
        $class = new NakedObjectSpecificationStub('NakedPhp\Stubs\User', array('doSomething' => new NakedObjectAction('doSomething')));

        $methods = $this->_methodMerger->getApplicableMethods($class);
        $this->assertTrue(isset($methods['doSomething']));
        $this->assertTrue($this->_methodMerger->hasMethod($class, 'doSomething'));
        $this->assertFalse($this->_methodMerger->hasMethod($class, 'doSomethingWhichDoesNotExist'));
    }

    public function testCallsAServiceMethodAsIfItWereAnHiddenOneOnTheEntityClass()
    {
        $this->markTestIncomplete();
    }

    public function testListsMethodsOfTheServiceClassesWhichTakeEntityAsAnArgument()
    {
        $this->_makeProcessMethodAvailable();
        $class = $this->_getEmptyEntityClass();
        $methods = $this->_methodMerger->getApplicableMethods($class);
        $this->assertTrue(isset($methods['process']));
    }

    /**
     * TODO: refactor implementation to keep ALL facets
     * @depends testListsMethodsOfTheServiceClassesWhichTakeEntityAsAnArgument
     */
    public function testKeepsInvocationFacetOnRebuiltMethods()
    {
        $this->_makeProcessMethodAvailable();
        $this->_serviceClass->getObjectAction('process')->addFacet(new Invocation('process'));
        $class = $this->_getEmptyEntityClass();
        $methods = $this->_methodMerger->getApplicableMethods($class);

        $this->assertTrue(isset($methods['process']));
        $this->assertNotNull($methods['process']->getFacet('Action\Invocation'));
    }

    public function testDoesNotListEntityAsAnArgumentOfAServiceMethod()
    {
        $this->_makeProcessMethodAvailable();
        $class = $this->_getEmptyEntityClass();
        $methods = $this->_methodMerger->getApplicableMethods($class);
        $params = $methods['process']->getParameters();
        $this->assertEquals(0, count($params));
    }

    public function testCallsAServiceMethodAsIfItWereOnTheEntityClass()
    {
        $this->_makeProcessMethodAvailable();
        $service = new NakedBareObject($this, $this->_serviceClass);
        $this->_providerMock->expects($this->once())
                            ->method('getService')
                            ->with('theService')
                            ->will($this->returnValue($service));
        $class = $this->_getEmptyEntityClass();
        $this->_entity = new \NakedPhp\Stubs\User;
        $user = new NakedBareObject($this->_entity, $class);
        $methods = $this->_methodMerger->call($user, 'process');
        $this->assertTrue($this->_callbackCalled);
    }

    public function process($argument)
    {
        $this->assertEquals($this->_entity, $argument);
        $this->_callbackCalled = true;
    }

    private function _makeProcessMethodAvailable()
    {
        $this->_serviceClass = new NakedObjectSpecificationStub('', array(
            'process' => new NakedObjectAction('process', array(
                'objectToProcess' => new NakedObjectActionParameter('NakedPhp\Stubs\User', 'objectToProcess')
            ))
        ));
        $this->_setAvailableServiceClasses(array(
            'theService' => $this->_serviceClass
        ));
    }

    public function testCallsAEntityMethodAutomaticallyInjectingServices()
    {
        $this->_setAvailableServiceClasses(array(
            'NakedPhp\Stubs\UserFactory' => new NakedObjectSpecificationStub('NakedPhp\Stubs\UserFactory')
        ));
        $service = new \NakedPhp\Stubs\UserFactory();
        $this->_providerMock->expects($this->once())
                            ->method('getService')
                            ->with('NakedPhp\Stubs\UserFactory')
                            ->will($this->returnValue($service));

        $class = $this->_getEntityClassWithCreateNewMethod();
        $no = new NakedBareObject($this, $class);

        $methods = $this->_methodMerger->getApplicableMethods($class);
        $this->assertNull($methods['createNew']->getFacet('Action\Invocation'));

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
        return new NakedObjectSpecificationStub('NakedPhp\Stubs\User', array(
            'createNew' => new NakedObjectAction('createNew', array(
                'userFactory' => new NakedObjectActionParameter('NakedPhp\Stubs\UserFactory', 'userFactory'),
                'name' => $name = new NakedObjectActionParameter('string', 'name')
            ))
        ));
    }

    private function _getEmptyEntityClass()
    {
        return  new NakedObjectSpecificationStub('NakedPhp\Stubs\User', array());
    }

    public function testExtractsMetadataForAMethod()
    {
        $this->_setAvailableServiceClasses(array());
        $methods = array(
            'doSomething' => $expectedMethod = new NakedObjectAction(''),
        );
        $class = new NakedObjectSpecificationStub('NakedPhp\Stubs\User', $methods);

        $this->assertTrue($this->_methodMerger->hasMethod($class, 'doSomething'));
        $this->assertSame($expectedMethod,
                          $this->_methodMerger->getObjectAction($class, 'doSomething'));
    }

    public function testExtractsBuiltMetadataForAServiceMethod()
    {
        $this->_serviceClass = new NakedObjectSpecificationStub('', array(
            'block' => new NakedObjectAction('block', array(
                'user' => new NakedObjectActionParameter('NakedPhp\Stubs\User', 'user'),
                'days' => $days = new NakedObjectActionParameter('integer', 'days')
            ))
        ));
        $this->_setAvailableServiceClasses(array(
            'theService' => $this->_serviceClass
        ));

        $class = $this->_getEmptyEntityClass();
        $this->assertTrue($this->_methodMerger->hasMethod($class, 'block'));
        $method = $this->_methodMerger->getObjectAction($class, 'block');
        $this->assertEquals(array('days' => $days), $method->getParameters());
    }

    public function testExtractsBuiltMetadataForAEntityMethodWhichRequireAService()
    {
        $this->_setAvailableServiceClasses(array(
            'NakedPhp\Stubs\UserFactory' => new NakedObjectSpecificationStub('NakedPhp\Stubs\UserFactory')
        ));

        $class = $this->_getEntityClassWithCreateNewMethod();

        $this->assertTrue($this->_methodMerger->hasMethod($class, 'createNew'));
        $method = $this->_methodMerger->getObjectAction($class, 'createNew');
        $this->assertEquals(array('name' => new NakedObjectActionParameter('string', 'name')), $method->getParameters());
    }

    public function testSupportsMetadataBuildingWhenMoreThanOneParameterIsAService()
    {
        $this->markTestIncomplete();
    }
}
