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
use NakedPhp\MetaModel\NakedObject;
use NakedPhp\Mvc\EntityContainer;
use NakedPhp\Stubs\NakedObjectStub;

class BareContainerTest extends \PHPUnit_Framework_TestCase
{
    private $_container;

    public function setUp()
    {
        $this->_stateDiscoverer = new DummyStateDiscoverer();
        $this->_container = new BareContainer($this->_stateDiscoverer);
    }

    private function _getEntity()
    {
        return new NakedObjectStub(new \stdClass);
    }

    public function testAddsAnObjectAndReturnsKey()
    {
        $entity = $this->_getEntity();
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

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testClearsItselfFromAllEntities()
    {
        $entity = $this->_getEntity();
        $this->_container->add($entity);
        $this->_container->clear(); 
        $this->_assertContainerIsEmpty();
    }

    public function testReplacesAnObject()
    {
        $entity = $this->_getEntity();
        $key = $this->_container->add($entity);
        $newEntity = $this->_getEntity();
        $this->_container->replace($key, $newEntity);
        $this->assertSame($newEntity, $this->_container->get($key));

    }

    public function testSetsStateOfAddedObjectsAsNewIfTheStateDiscovererSaysItIsTransient()
    {
        $entity = $this->_getEntity();
        $this->_stateDiscoverer->setTransient(true);
        $key = $this->_container->add($entity);
        $this->assertEquals(EntityContainer::STATE_NEW, $this->_container->getState($key));
    }

    public function testSetsStateOfAddedObjectsAsDetachedIfTheStateDiscovererSaysItIsNotTransient()
    {
        $entity = $this->_getEntity();
        $this->_stateDiscoverer->setTransient(false);
        $key = $this->_container->add($entity);
        $this->assertEquals(EntityContainer::STATE_DETACHED, $this->_container->getState($key));
    }

    public function testAllowsManualStateSetting()
    {
        $entity = $this->_getEntity();
        $key = $this->_container->add($entity);
        $this->_container->setState($key, EntityContainer::STATE_DETACHED);
        $this->assertEquals(EntityContainer::STATE_DETACHED, $this->_container->getState($key));

        $this->_container->setState($key, EntityContainer::STATE_REMOVED);
        $this->assertEquals(EntityContainer::STATE_REMOVED, $this->_container->getState($key));
    }

    public function testAllowsManualStateSettingDuringAddition()
    {
        $entity = $this->_getEntity();
        $key = $this->_container->add($entity, EntityContainer::STATE_DETACHED);
        $this->assertEquals(EntityContainer::STATE_DETACHED, $this->_container->getState($key));
    }

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testRecognizesNewObjects()
    {
        $entity = $this->_getEntity();
        $this->assertFalse($this->_container->contains($entity));
    }

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testRecognizesAnAddedObject()
    {
        $entity = $this->_getEntity();
        $key = $this->_container->add($entity);
        $this->assertTrue((bool) $this->_container->contains($entity));
    }

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testAddsAnObjectIdempotently()
    {
        $entity = $this->_getEntity();
        $key = $this->_container->add($entity);
        $anotherKey = $this->_container->add($entity);
        $this->assertSame($key, $anotherKey);
    }

    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testAddsAnEqualObjectIdempotently()
    {
        $wrapped = new \stdClass;
        $entity = new NakedObjectStub($wrapped);
        $equalEntity = new NakedObjectStub($wrapped);
        $key = $this->_container->add($entity);
        $anotherKey = $this->_container->add($equalEntity);
        $this->assertSame($key, $anotherKey);
    }


    /**
     * @depends testAddsAnObjectAndReturnsKey
     */
    public function testSerializationMustNotAffectIdempotentAddition()
    {
        $entity = $this->_getEntity();
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

class DummyStateDiscoverer implements StateDiscoverer
{
    private $_dummyResult = false;

    public function isTransient(NakedObject $object)
    {
        return $this->_dummyResult;
    }

    public function setTransient($result)
    {
        $this->_dummyResult = $result;
    }
}
