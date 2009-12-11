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
use NakedPhp\Mvc\EntityContainer\UnwrappedContainer;
use NakedPhp\Stubs\User;

/**
 * Exercise the Doctrine storage driver, which should reflect to the database
 * the changes in entities kept in an EntityContainer.
 */
class DoctrineTest extends \PHPUnit_Framework_TestCase
{
    private $_storage;

    public function setUp()
    {
        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
        $config->setProxyDir('/NOTUSED/Proxies');
        $config->setProxyNamespace('StubsProxies');

        $connectionOptions = array(
            'driver' => 'pdo_sqlite',
            'path' => '/var/www/nakedphp/tests/database.sqlite'
        );

        $this->_em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
        $this->_regenerateSchema();

        $this->_storage = new Doctrine($this->_em);
    }

    private function _regenerateSchema()
    {
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->_em);
        $classes = array(
            $this->_em->getClassMetadata('NakedPhp\Stubs\User')
        );
        $tool->dropSchema($classes);
        $tool->createSchema($classes);
    }

    public function testSavesNewEntities()
    {
        $container = $this->_getContainer(array(
            'Picard' => EntityContainer::STATE_NEW
        ));
        $this->_storage->save($container);

        $this->_assertExistsOne('Picard');
    }

    /**
     * @depends testSavesNewEntities
     */
    public function testSavesIdempotently()
    {
        $container = $this->_getContainer(array(
            'Picard' => EntityContainer::STATE_NEW
        ));
        $this->_storage->save($container);

        $this->_simulateNewPage();
        $this->_storage->save($container);

        $this->_assertExistsOne('Picard');
    }

    public function testSavesUpdatedEntities()
    {
        $picard = $this->_getDetachedUser('Picard');
        $picard->setName('Locutus');
        $container = $this->_getContainer();
        $key = $container->add($picard, EntityContainer::STATE_DETACHED);
        $this->_storage->save($container);

        $this->_assertExistsOne('Locutus');
        $this->_assertNotExists('Picard');
    }

    public function testRemovesPreviouslySavedEntities()
    {
        $picard = $this->_getDetachedUser('Picard');
        $container = $this->_getContainer();

        $key = $container->add($picard, EntityContainer::STATE_REMOVED);
        $this->_storage->save($container);

        $this->_assertNotExists('Picard');
        $this->assertFalse($container->contains($picard));
    }

    private function _getNewUser($name)
    {
        $user = new User();
        $user->setName($name);
        return $user;
    }

    private function _getDetachedUser($name)
    {
        $user = $this->_getNewUser($name);
        $this->_em->persist($user);
        $this->_em->flush();
        $this->_em->detach($user);
        return $user;
    }

    private function _getContainer(array $fixture = array())
    {
        $container = new UnwrappedContainer;
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
