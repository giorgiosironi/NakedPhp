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

namespace NakedPhp\Mvc\EntityContainer;
use NakedPhp\Mvc\EntityContainer;

class UnwrappedContainerTest extends \PHPUnit_Framework_TestCase
{
    private $_container;

    public function setUp()
    {
        $this->_container = new UnwrappedContainer();
    }

    public function testAddsAnObjectAndReturnsKey()
    {
        $entity = new \stdClass;
        $key = $this->_container->add($entity);
        $this->assertSame($entity, $this->_container->get($key));
        return $key;
    }

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testRemovesAnObject($key)
    {
        $this->_container->delete($key); 
        $this->_assertContainerIsEmpty();
    }

    public function testReplacesAnObject()
    {
        $entity = new \stdClass;
        $key = $this->_container->add($entity);
        $newEntity = new \stdClass;
        $this->_container->replace($key, $newEntity);
        $this->assertSame($newEntity, $this->_container->get($key));

    }

    public function testSetsStateOfAddedObjectsAsNew()
    {
        $entity = new \stdClass;
        $key = $this->_container->add($entity);
        $this->assertEquals(EntityContainer::STATE_NEW, $this->_container->getState($key));
    }

    public function testAllowsManualStateSetting()
    {
        $entity = new \stdClass;
        $key = $this->_container->add($entity);
        $this->_container->setState($key, EntityContainer::STATE_DETACHED);
        $this->assertEquals(EntityContainer::STATE_DETACHED, $this->_container->getState($key));

        $this->_container->setState($key, EntityContainer::STATE_REMOVED);
        $this->assertEquals(EntityContainer::STATE_REMOVED, $this->_container->getState($key));
    }

    public function testAllowsManualStateSettingDuringAddition()
    {
        $entity = new \stdClass;
        $key = $this->_container->add($entity, EntityContainer::STATE_DETACHED);
        $this->assertEquals(EntityContainer::STATE_DETACHED, $this->_container->getState($key));
    }

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testRecognizesNewObjects()
    {
        $entity = new \stdClass;
        $this->assertFalse($this->_container->contains($entity));
    }

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testRecognizesAnAddedObject()
    {
        $entity = new \stdClass;
        $key = $this->_container->add($entity);
        $this->assertTrue((boolean) $this->_container->contains($entity));
    }

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testAddsAnObjectIdempotently()
    {
        $entity = new \stdClass;
        $key = $this->_container->add($entity);
        $anotherKey = $this->_container->add($entity);
        $this->assertSame($key, $anotherKey);
    }

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testSerializationMustNotAffectIdempotentAddition()
    {
        $entity = new \stdClass;
        $key = $this->_container->add($entity);
        $serialized = serialize($this->_container);
        unset($this->_container);
        $container = unserialize($serialized);
        $entity = $container->get($key);
        $anotherKey = $container->add($entity);
        $this->assertSame($key, $anotherKey);
    }

    private function _assertContainerIsEmpty()
    {
        foreach ($this->_container as $object) {
            $this->asserTrue(false);
        } 
    }
}
