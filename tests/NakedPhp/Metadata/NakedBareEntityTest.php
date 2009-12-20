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
use NakedPhp\Stubs\Phonenumber;

class NakedBareEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testRetainsClassMetadata()
    {
        $no = new NakedBareEntity($this, $class = new NakedEntityClass());
        $this->assertSame($class, $no->getClass());
    }

    public function testDelegatesFieldManagementToTheInnerClassInstance()
    {
        $no = new NakedBareEntity($this, $class = new NakedEntityClass('', array(), array('name' => 'Name')));
        $this->assertSame('Name', $no->getField('name'));
    }

    public function testReturnsTheStateOfTheObject()
    {
        $no = new NakedBareEntity($this, $class = new NakedEntityClass('', array(), array('nickname' => null)));
        $this->assertEquals(array('nickname' => 'dummy'), $no->getState());
    }

    public function testSetsTheStateOfTheObject()
    {
        $data = array('nickname' => 'dummy');
        $user = $this->getMock('NakedPhp\Stubs\User', array('setNickname'));
        $user->expects($this->once())
             ->method('setNickname')
             ->with('dummy');
        $no = new NakedBareEntity($user, null);
        $no->setState($data);
    }

    public function testSetsTheStateOfTheObjectAlsoWithARelation()
    {
        $data = array('phonenumber' => new NakedBareEntity($phonenumber = new Phonenumber));
        $user = $this->getMock('NakedPhp\Stubs\User', array('setPhonenumber'));
        $user->expects($this->once())
             ->method('setPhonenumber')
             ->with($phonenumber);
        $no = new NakedBareEntity($user, null);
        $no->setState($data);
    }

    public function testProxiesToTheClassForObtainingApplicableMethods()
    {
        $class = $this->getMock('NakedPhp\Metadata\NakedEntityClass', array('getMethods'));
        $class->expects($this->any())
             ->method('getMethods')
             ->will($this->returnValue(array('dummy' => 'DummyMethod')));

        $no = new NakedBareEntity($this, $class);
        $this->assertEquals(array('dummy' => 'DummyMethod'), $no->getMethods());
        $this->assertEquals('DummyMethod', $no->getMethod('dummy'));
        $this->assertTrue($no->hasMethod('dummy'));
        $this->assertFalse($no->hasMethod('notExistentMethodName'));
    }

    public function testProxiesToTheClassForFacetHolding()
    {
        $class = $this->getMock('NakedPhp\Metadata\NakedEntityClass', array('getFacet'));
        $class->expects($this->once())
             ->method('getFacet')
             ->with('Dummy')
             ->will($this->returnValue('foo'));

        $no = new NakedBareEntity(null, $class);
        $this->assertEquals('foo', $no->getFacet('Dummy'));
    }

    public function testIsTraversable()
    {
        $no = new NakedBareEntity(null, null);
        $this->assertTrue($no instanceof \Traversable);
    }

    /**
     * @depends testReturnsTheStateOfTheObject
     */
    public function testIsTraversableProxyingToTheEntityState()
    {
        $class = new NakedEntityClass('', array(), array('nickname' => null));
        $no = new NakedBareEntity($this, $class);
        $this->assertEquals('dummy', $no->getIterator()->current());
    }

    /** self-shunting */
    public function getNickname()
    {
        return 'dummy';
    }
}
