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
 * @package    NakedPhp_Storage
 */

namespace NakedPhp\Storage;
use Doctrine\ORM\UnitOfWork;
use NakedPhp\Mvc\EntityContainer;
use NakedPhp\Mvc\EntityContainer\BareContainer;
use NakedPhp\ProgModel\Facet;
use NakedPhp\Stubs\DummyCollectionFacet;
use NakedPhp\Stubs\NakedObjectStub;
use NakedPhp\Stubs\User;

/**
 * Exercise the Doctrine storage driver, which should reflect to the database
 * the changes in entities kept in an EntityContainer.
 */
class DoctrineTest extends AbstractDoctrineTest
{
    private $_storage;

    public function setUp()
    {
        parent::setUp();

        $this->_storage = new Doctrine($this->_em);
    }

    public function testSavesNewEntities()
    {
        $container = $this->_getContainer(array(
            'Picard' => EntityContainer::STATE_NEW
        ));

        $this->_storage->merge($container);
        $result = $this->_storage->save($container);

        $this->_assertExistsOne('Picard');
        $this->assertEquals(1, $result[Doctrine::ACTION_NEW]);
    }

    /**
     * It is assumed the entities have the same state, which is
     * the state of the Collection.
     */
    public function testAcceptsCollectionsAndSavesTheSingleEntities()
    {
        $array = array(
            $this->_getNewUser('Picard'),
            $this->_getNewUser('Riker')
        );
        $collection = new NakedObjectStub();
        $collection->addFacet(new DummyCollectionFacet($array));
        $container = $this->_getContainer();
        $key = $container->add($collection);
        $container->setState($key, EntityContainer::STATE_NEW);

        $this->_storage->merge($container);
        $result = $this->_storage->save($container);

        $this->assertEquals(2, $result[Doctrine::ACTION_NEW]);
        $this->_assertExistsOne('Picard');
        $this->_assertExistsOne('Riker');
    }

    /**
     * @depends testSavesNewEntities
     */
    public function testSavesIdempotently()
    {
        $container = $this->_getContainer(array(
            'Picard' => EntityContainer::STATE_NEW
        ));
        $this->_storage->merge($container);
        $this->_storage->save($container);

        $this->_simulateNewPage();
        $this->_storage->merge($container);
        $this->_storage->save($container);

        $this->_assertExistsOne('Picard');
    }

    public function testSavesUpdatedEntities()
    {
        $picard = $this->_getDetachedUser('Picard');
        $picard->setName('Locutus');
        $container = $this->_getContainer();
        $key = $container->add($picard, EntityContainer::STATE_DETACHED);

        $this->_storage->merge($container);
        $result = $this->_storage->save($container);

        $this->assertEquals(1, $result[Doctrine::ACTION_UPDATED]);
        $this->_assertExistsOne('Locutus');
        $this->_assertNotExists('Picard');
    }

    public function testRemovesPreviouslySavedEntities()
    {
        $picard = $this->_getDetachedUser('Picard');
        $container = $this->_getContainer();

        $key = $container->add($picard, EntityContainer::STATE_REMOVED);

        $this->_storage->merge($container);
        $result = $this->_storage->save($container);

        $this->assertEquals(1, $result[Doctrine::ACTION_REMOVED]);
        $this->_assertNotExists('Picard');
        $this->assertFalse($container->contains($picard));
    }

    public function testDoesNotChangeEntitiesStateIfTheyAreNotSaved()
    {
        $container = $this->_getContainer();
        $container->add($user = $this->_getNewUser(null));
        $container->add($detachedUser = $this->_getDetachedUser('Picard'), EntityContainer::STATE_DETACHED);
        $detachedUser->setName(null);
        try {
            $this->_storage->merge($container);
            $this->_storage->save($container);
        } catch (\NakedPhp\Storage\Exception $e) {
            $this->assertEquals(EntityContainer::STATE_NEW, $container->getState(1)); 
            $this->assertEquals(EntityContainer::STATE_DETACHED, $container->getState(2)); 
            // removed need to stay if not removed
            $this->markTestIncomplete();
        }
        $this->fail('User is saved with null properties.');
    }

    /**
     * @return NakedObject
     */
    private function _getNewUser($name)
    {
        $user = new User();
        $user->setName($name);
        return new NakedObjectStub($user);
    }

    private function _getDetachedUser($name)
    {
        $user = $this->_getNewUser($name);
        $this->_em->persist($user->getObject());
        $this->_em->flush();
        $this->_em->detach($user->getObject());
        return $user;
    }

    /**
     * @param array $fixtures   names (strings) that points to EntityContainer::STATE_*
     * @return EntityContainer
     */
    private function _getContainer(array $fixture = array())
    {
        $container = new BareContainer;
        foreach ($fixture as $name => $state) {
            $user = $this->_getNewUser($name);
            $key = $container->add($user);
            $container->setState($key, $state);
        }
        return $container;
    }

    private function _assertExistsOne($name)
    {
        $this->_howMany($name, 1);
    }

    private function _assertNotExists($name)
    {
        $this->_howMany($name, 0);
    }

    private function _howMany($name, $number)
    {
        $q = $this->_em->createQuery("SELECT COUNT(u._id) FROM NakedPhp\Stubs\User u WHERE u._name = '$name'");
        $result = $q->getSingleScalarResult();
        $this->assertEquals($number, $result, "There are $result instances of $name saved instead of $number.");
    }

    private function _simulateNewPage()
    {
        $this->_em->clear(); // detach all entities
    }
}
