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
use NakedPhp\MetaModel\NakedObject;

class FactoryProviderTest extends \PHPUnit_Framework_TestCase
{
    private $_SplQueueSpec;
    private $_provider;
    
    public function setUp()
    {
        $serviceSpecs = array(
            'stdClass'              => new NakedObjectSpecificationStub('stdClass'), 
            'SplQueue'              => $this->_SplQueueSpec = new NakedObjectSpecificationStub('SplQueue'),
            'My_NameSpace_SplQueue' => new NakedObjectSpecificationStub('My_NameSpace_SplQueue'),
            'My\NameSpace\SplQueue' => new NakedObjectSpecificationStub('My\NameSpace\SplQueue')
        );

        $serviceDiscovererMock = $this->getMock('NakedPhp\Reflect\ServiceDiscoverer');
        $serviceDiscovererMock->expects($this->any())
                              ->method('getServiceSpecifications')
                              ->will($this->returnValue($serviceSpecs));

        $this->_provider = new FactoryProvider($serviceDiscovererMock,
                                               $this);
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

    public function testInjectServiceMetaModelIntoInstances()
    {
        $service = $this->_provider->getService('SplQueue');
        $this->assertSame($this->_SplQueueSpec, $service->getSpecification());
    }

    public function testProxiesToDiscovererForServiceSpecifications()
    {
        $specs = $this->_provider->getServiceSpecifications();
        $this->assertEquals(4, count($specs));
    }

    
    /* self-shunting for user defined factory */
    public function getSplQueue()
    {
        $queue = new \SplQueue();
        $queue->enqueue('insertedDuringConstruction');
        return $queue;
    }
}
