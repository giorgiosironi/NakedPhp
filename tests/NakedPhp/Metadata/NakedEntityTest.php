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
 * @package    NakedPhp_Metadata
 */

namespace NakedPhp\Metadata;

class NakedEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testRetainsClassMetadata()
    {
        $no = new NakedEntity($this, $class = new NakedEntityClass(array(), array('name')));
        $this->assertSame($class, $no->getClass());
    }

    public function testReturnsTheStateOfTheObject()
    {
        $no = new NakedEntity($this, $class = new NakedEntityClass(array(), array('nickname' => null)));
        $this->assertEquals(array('nickname' => 'dummy'), $no->getState());
    }

    public function testSetsTheStateOfTheObject()
    {
        $data = array('nickname' => 'dummy');
        $user = $this->getMock('NakedPhp\Stubs\User', array('setNickname'));
        $user->expects($this->once())
             ->method('setNickname')
             ->with('dummy');
        $no = new NakedEntity($user, null);
        $no->setState($data);
    }

    public function testIsTraversable()
    {
        $no = new NakedEntity(null, null);
        $this->assertTrue($no instanceof \IteratorAggregate);
    }

    /**
     * @depends testReturnsTheStateOfTheObject
     */
    public function testIsTraversableProxyingToTheEntityState()
    {
        $class = new NakedEntityClass(array(), array('nickname' => null));
        $no = new NakedEntity($this, $class);
        $this->assertEquals('dummy', $no->getIterator()->current());
    }

    /** self-shunting */
    public function getNickname()
    {
        return 'dummy';
    }
}
