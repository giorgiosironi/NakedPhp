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
use NakedPhp\Metadata\NakedObject;

class EntityContainerTest extends \PHPUnit_Framework_TestCase
{
    private $_container;

    public function setUp()
    {
        $this->_container = new \NakedPhp\Service\EntityContainer();
    }

    public function testAddsAnObjectAndReturnsKey()
    {
        $no = new NakedObject(null);
        $key = $this->_container->add($no);
        $this->assertSame($no, $this->_container->get($key));
    }

    public function testRecognizesNewObjects()
    {
        $no = new NakedObject(null);
        $this->assertFalse($this->_container->contains($no));
    }

    public function testRecognizesAnAddedObject()
    {
        $no = new NakedObject(null);
        $key = $this->_container->add($no);
        $this->assertTrue((boolean) $this->_container->contains($no));
    }

    public function testAddsAnObjectIdempotently()
    {
        $no = new NakedObject(null);
        $key = $this->_container->add($no);
        $anotherKey = $this->_container->add($no);
        $this->assertSame($key, $anotherKey);
    }

    public function testAddsAnEqualsObjectIdempotently()
    {
        $wrapped = new \stdClass;
        $no = new NakedObject($wrapped);
        $key = $this->_container->add($no);
        $anotherKey = $this->_container->add(new NakedObject($wrapped));
        $this->assertSame($key, $anotherKey);
    }

    public function testSerializationMustNotAffectIdempotentAddition()
    {
        $no = new NakedObject(null);
        $key = $this->_container->add($no);
        $serialized = serialize($this->_container);
        unset($this->_container);
        $container = unserialize($serialized);
        $no = $container->get($key);
        $anotherKey = $container->add($no);
        $this->assertSame($key, $anotherKey);
    }
}
