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
        $no = new NakedBareEntity($this, $class = new NakedEntitySpecification());
        $this->assertSame($class, $no->getSpecification());
    }

    public function testDelegatesFieldManagementToTheInnerClassInstance()
    {
        $no = new NakedBareEntity($this, $class = new NakedEntitySpecification('', array(), array('name' => 'Name')));
        $this->assertSame('Name', $no->getField('name'));
    }

    public function testUnwrapsTheWrappedEntity()
    {
        $no = new NakedBareEntity($this);

        $this->assertSame($this, $no->getObject());
    }

    public function testReturnsTheStateOfTheObject()
    {
        $no = new NakedBareEntity($this, $class = new NakedEntitySpecification('', array(), array('nickname' => null)));
        $this->assertEquals(array('nickname' => 'dummy'), $no->getState());
    }

    /**
     * FIX: NakedBareEntity should not be used for scalar.
     * Think of a new adapter that implements NakedObject.
     */
    public function testSetsTheStateOfTheObject()
    {
        $data = array('nickname' => new NakedBareEntity('dummy'));
        $field = $this->getMock('NakedPhp\Metadata\OneToOneAssociation');
        $field->expects($this->once())
              ->method('setAssociation');
        $class = new NakedEntitySpecification(null, array(), array('nickname' => $field));
        $no = new NakedBareEntity(null, $class);
        $no->setState($data);
    }

    public function testSetsTheStateOfTheObjectAlsoWithARelation()
    {
        $data = array('phonenumber' => $phonenumber = new NakedBareEntity(new Phonenumber));

        $field = $this->getMock('NakedPhp\Metadata\OneToOneAssociation');
        $class = new NakedEntitySpecification(null, array(), array('phonenumber' => $field));
        $no = new NakedBareEntity(null, $class);

        $field->expects($this->once())
              ->method('setAssociation')
              ->with($no, $phonenumber);

        $no->setState($data);
    }

    public function testProxiesToTheClassForObtainingApplicableMethods()
    {
        $class = $this->getMock('NakedPhp\Metadata\NakedEntitySpecification', array('getObjectActions'));
        $class->expects($this->any())
             ->method('getObjectActions')
             ->will($this->returnValue(array('dummy' => 'DummyMethod')));

        $no = new NakedBareEntity($this, $class);
        $this->assertEquals(array('dummy' => 'DummyMethod'), $no->getObjectActions());
        $this->assertEquals('DummyMethod', $no->getObjectAction('dummy'));
        $this->assertTrue($no->hasMethod('dummy'));
        $this->assertFalse($no->hasMethod('notExistentMethodName'));
    }

    public function testProxiesToTheClassForFacetHolding()
    {
        $class = $this->getMock('NakedPhp\Metadata\NakedEntitySpecification', array('getFacet'));
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
        $class = new NakedEntitySpecification('', array(), array('nickname' => null));
        $no = new NakedBareEntity($this, $class);
        $this->assertEquals('dummy', $no->getIterator()->current());
    }

    /** self-shunting */
    public function getNickname()
    {
        return 'dummy';
    }
}
