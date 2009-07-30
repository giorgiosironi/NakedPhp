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
use NakedPhp\Reflect\ServiceReflector;

class ServiceDiscovererTest extends \PHPUnit_Framework_TestCase
{
    public function testExcludesClassesWhichAreNotServices()
    {
        $reflectorStub = new ServiceReflectorStub(array());
        $discoverer = new ServiceDiscoverer($reflectorStub);
        $services = $discoverer->getList(__DIR__ . '/../Stubs/');
        $this->assertEquals(array(), $services);
    }

    public function testListsAllAnnotatedClassesInAFolder()
    {
        $reflectorStub = new ServiceReflectorStub(array('UserFactory'));
        $discoverer = new ServiceDiscoverer($reflectorStub);
        $services = $discoverer->getList(__DIR__ . '/../Stubs/');
        $this->assertEquals(array('UserFactory'), $services);
    }

    public function testAcceptsAPrefixForTheClasses()
    {
        $reflectorStub = new ServiceReflectorStub(array('NakedPhp\\Stubs\\UserFactory'));
        $discoverer = new ServiceDiscoverer($reflectorStub);
        $services = $discoverer->getList(__DIR__ . '/../Stubs/', 'NakedPhp\\Stubs\\');
        $this->assertEquals(array('NakedPhp\\Stubs\\UserFactory'), $services);
    }
}

/**
 * Stub for ServiceReflector
 * <code>
 * new ServiceReflectorStub(array('NakedPhp\ClassName')); 
 *  //NakedPhp\ClassName will be recognized as service
 * </code>
 */
class ServiceReflectorStub extends \NakedPhp\Reflect\ServiceReflector
{
    private $_serviceClasses;

    public function __construct($serviceClasses)
    {
        $this->_serviceClasses = $serviceClasses;
    }

    public function isService($class)
    {
        return in_array($class, $this->_serviceClasses);
    }
}
