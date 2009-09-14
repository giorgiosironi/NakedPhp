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
use NakedPhp\Metadata\NakedService;

class EmptyConstructorsServiceProviderTest extends \PHPUnit_Framework_TestCase implements ServiceDiscoverer
{
    private $_serviceClasses = array('stdClass', 'SplQueue');

    public function testIteratesOverAllServices()
    {
        $provider = new EmptyConstructorsServiceProvider($this);
        $classes = $provider->getServiceClasses();
        $this->assertEquals($this->_serviceClasses, array_keys($classes));
    }

    public function testInstancesServices()
    {
        $provider = new EmptyConstructorsServiceProvider($this);
        $ns = $provider->getService('SplQueue');
        $this->assertEquals('SplQueue', $ns->getClassName());
    }

    public function testProvidesServiceMetadata()
    {
        $this->markTestIncomplete();
        /*
        $provider = new EmptyConstructorsServiceProvider($this, $reflector);
        $classes = $provider->getServiceClasses();
        foreach ($classes as ...) {
        }
        */
    }
    
    /* self-shunting */
    public function getList()
    {
        return $this->_serviceClasses;
    }
}
