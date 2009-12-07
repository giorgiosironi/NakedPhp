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
use NakedPhp\Metadata\NakedServiceClass;

class EmptyConstructorsProviderTest extends \PHPUnit_Framework_TestCase implements \NakedPhp\Service\ServiceDiscoverer
{
    private $_serviceClasses = array('stdClass', 'SplQueue');

    /** @var NakedPhp\Metadata\NakedServiceClass */
    private $_originalClass;

    /**
     * @var EmptyConstructorsServiceProvider
     */
    private $_provider;
    
    public function setUp()
    {
        $this->_originalClass = new NakedServiceClass();
        $serviceReflectorMock = $this->getMock('NakedPhp\Reflect\ServiceReflector', array('analyze'));
        $serviceReflectorMock->expects($this->any())
                             ->method('analyze')
                             ->will($this->returnValue($this->_originalClass));
        $this->_provider = new EmptyConstructorsProvider($this, $serviceReflectorMock);
    }

    public function testIteratesOverAllServices()
    {
        $classes = $this->_provider->getServiceClasses();
        $this->assertEquals($this->_serviceClasses, array_keys($classes));
    }

    public function testInstancesServices()
    {
        $ns = $this->_provider->getService('SplQueue');
        $ns->enqueue('foo');
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
        $service = $this->_provider->getService('stdClass');
        $this->assertSame($this->_originalClass, $service->getClass());
    }
    
    /* self-shunting */
    public function getList()
    {
        return $this->_serviceClasses;
    }
}
