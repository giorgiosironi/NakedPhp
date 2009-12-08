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
use NakedPhp\Mvc\EntityContainer;
use NakedPhp\Stubs\User;

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

        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->_em);
        $classes = array(
            $this->_em->getClassMetadata('NakedPhp\Stubs\User')
        );
        $tool->dropSchema($classes);
        $tool->createSchema($classes);
        $this->_storage = new Doctrine($this->_em);
    }

    public function testSavesNewEntities()
    {
        $container = $this->_getContainer(array(
            'John' => EntityContainer::STATE_NEW
        ));
        $this->_storage->process($container);

        $this->_exists('John');
        $this->_notExists('Trudy');
    }

    private function _exists($name)
    {
        $this->_howMany($name, 1);
    }

    private function _notExists($name)
    {
        $this->_howMany($name, 0);
    }

    private function _howMany($name, $number)
    {
        $q = $this->_em->createQuery("SELECT COUNT(u._id) FROM NakedPhp\Stubs\User u WHERE u._name = '$name'");
        $result = $q->getSingleScalarResult();
        $this->assertEquals($number, $result);
    }

    private function _getContainer(array $fixture)
    {
        $container = new EntityContainer;
        foreach ($fixture as $name => $state) {
            $user = $this->_getUser($name);
            $key = $container->add($user);
            $container->setState($key, $state);
        }
        return $container;
    }

    private function _getUser($name)
    {
        $user = new User();
        $user->setName('John');
        return $user;
    }
}
