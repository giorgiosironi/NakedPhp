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
use NakedPhp\Stubs\User;

class AbstractFactoryAndRepositoryTest extends AbstractDoctrineTest
{
    public function setUp()
    {
        parent::setUp();
        $this->_far = new SampleSubclass($this->_em);
    }

    public function testCreatesNewEntities()
    {
        $object = $this->_far->createNew();
        $this->assertTrue($object instanceof User);
    }

    public function testFindsAnEntityById()
    {
        $user = new User;
        $user->setName('John');
        $this->_em->persist($user);
        $this->_em->flush();

        $found = $this->_far->find($user->getId());
        $this->assertSame($found, $user);
    }

    public function testFindsAllEntitiesOfTheSameType()
    {
        $user = new User;
        $user->setName('John');
        $this->_em->persist($user);
        $user = new User;
        $user->setName('Mark');
        $this->_em->persist($user);
        $this->_em->flush();

        $result = $this->_far->findAll();
        $this->assertEquals(2, count($result));
    }
}

class SampleSubclass extends AbstractFactoryAndRepository
{
    protected $_entityClassName = 'NakedPhp\Stubs\User';
}
