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

class ServiceIteratorTest extends \PHPUnit_Framework_TestCase implements ServiceProvider
{
    public function testIteratesOverAllServices()
    {
        $iterator = new ServiceIterator($this);
        $array = array();
        foreach ($iterator as $i => $value) {
            $array[$i] = $value;
        }
        $this->assertEquals(array('A' => new \stdClass, 'B' => new \stdClass, 'C' => new \stdClass), $array);
    }
    
    /* self-shunting */
    public function getServiceSpecifications()
    {
        return array('A' => null, 'B' => null, 'C' => null);
    }

    public function getService($className)
    {
        return new \stdClass;
    }
}
