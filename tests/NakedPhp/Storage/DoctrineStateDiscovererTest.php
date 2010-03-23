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
use NakedPhp\Stubs\DummyCollectionFacet;
use NakedPhp\Stubs\NakedObjectStub;
use NakedPhp\Stubs\User;

class DoctrineStateDiscovererTest extends AbstractDoctrineTest
{
    private $_stateDiscoverer;

    public function setUp()
    {
        parent::setUp();

        $this->_stateDiscoverer = new DoctrineStateDiscoverer($this->_em);
    }

    public function testRecognizesTransientEntities()
    {
        $no = new NakedObjectStub(new User);
        $this->assertTrue($this->_stateDiscoverer->isTransient($no));
    }

    public function testRecognizesNonTransientEntities()
    {
        $user = new User();
        $this->_em->persist($user);
        $no = new NakedObjectStub($user);
        $this->assertFalse($this->_stateDiscoverer->isTransient($no));
    }

    public function testRecognizesCollectionsAsNotTransientIfAtLeastOneElementIsNonTransient()
    {
        $array = new NakedObjectStub();
        $user = new User();
        $this->_em->persist($user);
        $content = array(
            new NakedObjectStub($user),
            new NakedObjectStub(new User)
        );
        $array->addFacet(new DummyCollectionFacet($content));

        $this->assertFalse($this->_stateDiscoverer->isTransient($array));
    }
}
