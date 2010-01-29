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

namespace NakedPhp\Service\Provider;
use NakedPhp\Stubs\NakedObjectSpecificationStub;
use NakedPhp\Metadata\NakedObject;

class FactoryProviderTest extends \PHPUnit_Framework_TestCase implements \NakedPhp\Service\ServiceDiscoverer
{
    private $_serviceClasses = array('stdClass', 'SplQueue');

    /** @var NakedPhp\Metadata\NakedObjectSpecification */
    private $_originalClass;

    /**
     * @var FactoryProvider
     */
    private $_provider;
    
    public function setUp()
    {
        $this->_originalClass = new NakedObjectSpecificationStub();
        $serviceReflectorMock = $this->getMock('NakedPhp\Reflect\ServiceReflector', array('analyze'));
        $serviceReflectorMock->expects($this->any())
                             ->method('analyze')
                             ->will($this->returnValue($this->_originalClass));
        $this->_provider = new FactoryProvider($this,
                                               $serviceReflectorMock,
                                               $this);
    }

    public function testIteratesOverAllServices()
    {
        $classes = $this->_provider->getServiceClasses();
        $this->assertEquals($this->_serviceClasses, array_keys($classes));
    }

    public function testInstancesServicesUsingAUserDefinedFactory()
    {
        $ns = $this->_provider->getService('SplQueue');
        $this->_assertIsTheFactoryQueue($ns);
    }

    public function testSelectsGetterMethodOnTheFactoryUsingBasename_NamespaceSeparator()
    {
        $ns = $this->_provider->getService('My\NameSpace\SplQueue');
        $this->_assertIsTheFactoryQueue($ns);
    }

    public function testSelectsGetterMethodOnTheFactoryUsingBasename_UnderscoreSeparator()
    {
        $ns = $this->_provider->getService('My_NameSpace_SplQueue');
        $this->_assertIsTheFactoryQueue($ns);
    }

    private function _assertIsTheFactoryQueue(NakedObject $ns)
    {
        $value = $ns->__call('dequeue');
        $this->assertEquals('insertedDuringConstruction', $value);
    }

    public function testProvidesServiceMetadata()
    {
        $classes = $this->_provider->getServiceClasses();
        foreach ($classes as $serviceClass) {
            $this->assertSame($this->_originalClass, $serviceClass);
        }
    }

    public function testInjectServiceMetadataIntoInstances()
    {
        $service = $this->_provider->getService('SplQueue');
        $this->assertSame($this->_originalClass, $service->getSpecification());
    }
    
    /* self-shunting for ServiceDiscoverer interface */
    public function getList()
    {
        return $this->_serviceClasses;
    }
    
    /* self-shunting for user defined factory */
    public function getSplQueue()
    {
        $queue = new \SplQueue();
        $queue->enqueue('insertedDuringConstruction');
        return $queue;
    }
}
