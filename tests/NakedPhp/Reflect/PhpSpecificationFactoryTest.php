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
 * @package    NakedPhp_Reflect
 */

namespace NakedPhp\Reflect;
use NakedPhp\ProgModel\PhpSpecification;
use NakedPhp\Reflect\ClassDiscoverer;

class PhpSpecificationFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatesPhpSpecificationObjectsGivenSomeClassNames()
    {
        $classDiscoverer = new DummyClassDiscoverer();
        $specFactory = new PhpSpecificationFactory($classDiscoverer);
        $expected = array(
            'My_Model_EntityA' => new PhpSpecification('My_Model_EntityA'),
            'My_Model_EntityB' => new PhpSpecification('My_Model_EntityB')
        );
        $this->assertEquals($expected, $specFactory->getSpecifications());
    }
}

class DummyClassDiscoverer implements ClassDiscoverer
{
    public function getList()
    {
        return array(
            'My_Model_EntityA',
            'My_Model_EntityB'
        );
    }
}
