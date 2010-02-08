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

/**
 * Base test case for class that interacts with the storage mechanism.
 */
abstract class AbstractDoctrineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em;

    public function setUp()
    {
        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
        $config->setProxyDir('/NOTUSED/Proxies');
        $config->setProxyNamespace('StubsProxies');

        $connectionOptions = array(
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/../../database.sqlite'
        );

        $this->_em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
        $this->_regenerateSchema();
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
}
