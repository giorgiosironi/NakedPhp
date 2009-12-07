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
 * @package    NakedPhp_Mvc
 */

namespace NakedPhp\Mvc;
use NakedPhp\Metadata\NakedBareEntity;

class EntityContainerTest extends \PHPUnit_Framework_TestCase
{
    private $_container;

    public function setUp()
    {
        $this->_container = new EntityContainer();
    }

    public function testAddsAnObjectAndReturnsKey()
    {
        $no = new NakedBareEntity(null);
        $key = $this->_container->add($no);
        $this->assertSame($no, $this->_container->get($key));
    }

    public function testSetsStateOfAddedObjectsAsNew()
    {
        $no = new NakedBareEntity(null);
        $key = $this->_container->add($no);
        $this->assertEquals(EntityContainer::STATE_NEW, $this->_container->getState($key));
    }

    public function testAllowsManualStateSetting()
    {
        $no = new NakedBareEntity(null);
        $key = $this->_container->add($no);
        $this->_container->setState($key, EntityContainer::STATE_DETACHED);
        $this->assertEquals(EntityContainer::STATE_DETACHED, $this->_container->getState($key));
    }

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testRecognizesNewObjects()
    {
        $no = new NakedBareEntity(null);
        $this->assertFalse($this->_container->contains($no));
    }

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testRecognizesAnAddedObject()
    {
        $no = new NakedBareEntity(null);
        $key = $this->_container->add($no);
        $this->assertTrue((boolean) $this->_container->contains($no));
    }

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testAddsAnObjectIdempotently()
    {
        $no = new NakedBareEntity(null);
        $key = $this->_container->add($no);
        $anotherKey = $this->_container->add($no);
        $this->assertSame($key, $anotherKey);
    }

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testAddsAnEqualsObjectIdempotently()
    {
        $wrapped = new \stdClass;
        $no = new NakedBareEntity($wrapped);
        $key = $this->_container->add($no);
        $anotherKey = $this->_container->add(new NakedBareEntity($wrapped));
        $this->assertSame($key, $anotherKey);
    }

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testSerializationMustNotAffectIdempotentAddition()
    {
        $no = new NakedBareEntity(null);
        $key = $this->_container->add($no);
        $serialized = serialize($this->_container);
        unset($this->_container);
        $container = unserialize($serialized);
        $no = $container->get($key);
        $anotherKey = $container->add($no);
        $this->assertSame($key, $anotherKey);
    }
}
