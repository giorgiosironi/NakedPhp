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
use NakedPhp\Reflect\ServiceDiscoverer;
use NakedPhp\Stubs\NakedObjectSpecificationStub;

class ConfiguredServiceDiscovererTest extends \PHPUnit_Framework_TestCase
{
    private $_specLoaderMock;
    private $_discoverer;

    public function setUp()
    {
        $this->_specLoaderMock = $this->getMock('NakedPhp\Reflect\SpecificationLoader');
        $this->_specLoaderMock->expects($this->once())
                              ->method('loadSpecification')
                              ->will($this->returnValue(new NakedObjectSpecificationStub('My_Model_Service')));

        $serviceNames = array('My_Model_Service');
        $this->_discoverer = new ConfiguredServiceDiscoverer($this->_specLoaderMock,
                                                             $serviceNames);
    }

    public function testRetrievesSpecificationsOfConfiguredServices()
    {
        $this->assertTrue($this->_discoverer instanceof ServiceDiscoverer);
        $serviceSpecs = $this->_discoverer->getServiceSpecifications();
        $this->assertEquals(1, count($serviceSpecs));
        $spec = current($serviceSpecs);
        $this->assertEquals('My_Model_Service', (string) $spec);

    }

    public function testMarkAsServiceTheSpecifications()
    {
        $serviceSpecs = $this->_discoverer->getServiceSpecifications();
        $spec = current($serviceSpecs);
        $this->assertTrue($spec->isService());
    }

    public function testCachesTheList()
    {
        $serviceSpecs = $this->_discoverer->getServiceSpecifications();
        $serviceSpecs = $this->_discoverer->getServiceSpecifications();
    }
}
