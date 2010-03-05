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
use NakedPhp\MetaModel\NakedObject;
use NakedPhp\Storage\AbstractFactoryAndRepository;
use NakedPhp\Stubs\NakedObjectSpecificationStub;
use NakedPhp\Stubs\NakedObjectStub;

class DefaultProviderTest extends \PHPUnit_Framework_TestCase
{
    private $_serviceSpec;
    private $_decorated;
    private $_provider;
    
    public function setUp()
    {
        $serviceSpecs = array(
            'stdClass'              => new NakedObjectSpecificationStub('stdClass'), 
            'SplQueue'              => $this->_SplQueueSpec = new NakedObjectSpecificationStub('SplQueue'),
            'NakedPhp\Service\Provider\DummyServiceThatRequiresEM' => 
                $this->_serviceSpec = new NakedObjectSpecificationStub('NakedPhp\Service\Provider\DummyServiceThatRequiresEM')
        );

        $this->_decorated = $this->getMock('NakedPhp\Service\ServiceProvider');
        $this->_decorated->expects($this->any())
                         ->method('getServiceSpecifications')
                         ->will($this->returnValue($serviceSpecs));
        $this->_em = new EntityManagerMock;
        $this->_provider = new DefaultProvider($this->_decorated, $this->_em);
    }

    /**
     * HACK: it tests also AbstractFactoryAndRepository a little
     */
    public function testInstancesServicesInjectingEntityManager()
    {
        $ns = $this->_provider->getService('NakedPhp\Service\Provider\DummyServiceThatRequiresEM');
        // how to test that EM is there?
        $this->assertTrue($ns->getObject() instanceof AbstractFactoryAndRepository);
    }

    public function testProxiesToDecoratedProviderForInstantiationOfNonSpecialServices()
    {
        $fake = new NakedObjectStub();
        $this->_decorated->expects($this->once())
                         ->method('getService')
                         ->will($this->returnValue($fake));
        $service = $this->_provider->getService('SplQueue');
        $this->assertTrue($service instanceof NakedObject);
        $this->assertSame($fake, $service);
    }

    public function testInjectServiceMetaModelIntoInstances()
    {
        $ns = $this->_provider->getService('NakedPhp\Service\Provider\DummyServiceThatRequiresEM');
        $this->assertSame($this->_serviceSpec, $ns->getSpecification());

    }

    public function testProxiesToDecoratedProviderForServiceSpecifications()
    {
        $specs = $this->_provider->getServiceSpecifications();
        $this->assertEquals(3, count($specs));
    }
}

class DummyServiceThatRequiresEM extends AbstractFactoryAndRepository
{
}

class EntityManagerMock extends \Doctrine\ORM\EntityManager
{
    public function __construct() {}
}
