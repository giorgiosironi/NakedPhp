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
use NakedPhp\Stubs\DummyFacet;
use NakedPhp\Stubs\User;

class OneToOneAssociationTest extends \PHPUnit_Framework_TestCase
{
    private $_actual;

    public function testRetainsTypeAndId()
    {
        $field = new OneToOneAssociation('string', 'name');
        $this->assertEquals('string', (string) $field->getType());
        $this->assertEquals('name', $field->getId());
    }

    public function testImplementsFacetHolderInterface()
    {
        $field = new OneToOneAssociation();
        $helper = new \NakedPhp\Test\FacetHolder($this);
        $helper->testIsFacetHolder($field);
    }

    public function testSetsAFieldOfTheObject()
    {
        $object = new NakedBareEntity($this);
        $associate = new NakedBareEntity($expected = new User);

        $field = new OneToOneAssociation('string', 'bestFriend');
        $field->setAssociation($object, $associate);
        $this->assertSame($expected, $this->_actual);
    }

    public function setBestFriend(User $user)
    {
        $this->_actual = $user;
    }

    /**
     * TODO: implement as a Facet
    public function testIsNotDefaultByDefault()
    {
        $param = new NakedObjectActionParameter('array', 'info');
        $this->assertFalse($param->getDefault());
    }
    */
}
