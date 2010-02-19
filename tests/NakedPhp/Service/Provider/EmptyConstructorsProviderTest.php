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

class EmptyConstructorsProviderTest extends \PHPUnit_Framework_TestCase
{
    private $_stdClassSpec;
    private $_provider;
    
    public function setUp()
    {
        $serviceClasses = array(
            'stdClass' => $this->_stdClassSpec = new NakedObjectSpecificationStub('stdClass'), 
            'SplQueue' => new NakedObjectSpecificationStub('SplQueue')
        );

        $serviceDiscovererMock = $this->getMock('NakedPhp\Reflect\ServiceDiscoverer');
        $serviceDiscovererMock->expects($this->any())
                              ->method('getServiceSpecifications')
                              ->will($this->returnValue($serviceClasses));
        $this->_provider = new EmptyConstructorsProvider($serviceDiscovererMock);
    }

    public function testInstancesServices()
    {
        $ns = $this->_provider->getService('SplQueue');
        $ns->enqueue('foo');
    }

    public function testInjectServiceMetaModelIntoInstances()
    {
        $service = $this->_provider->getService('stdClass');
        $this->assertSame($this->_stdClassSpec, $service->getSpecification());
    }

    public function testProxiesToDiscovererForServiceSpecifications()
    {
        $specs = $this->_provider->getServiceSpecifications();
        $this->assertEquals(2, count($specs));
    }
}
