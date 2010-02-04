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
 * @package    NakedPhp_ProgModel
 */

namespace NakedPhp\ProgModel;
use NakedPhp\Stubs\DummyFacet;

class PhpSpecificationTest extends \PHPUnit_Framework_TestCase
{
    public function testRetainsClassName()
    {
        $nc = new PhpSpecification('stdClass');
        $this->assertEquals('stdClass', $nc->getClassName());
    }

    public function testRetainsActionsList()
    {
        $nc = new PhpSpecification(null, $methods = array('doThis' => 'doThis', 'doThat'));
        $this->assertEquals($methods, $nc->getObjectActions());
        $this->assertTrue($nc->hasObjectAction('doThis'));
        $this->assertFalse($nc->hasObjectAction('doAnything'));
    }

    public function testAllowsInitializationOfActionsList()
    {
        $nc = new PhpSpecification(null, null);
        $methods = array(
            'doThisKey' => 'doThis',
            'doThatKey' => 'doThat'
        );

        $nc->initObjectActions($methods);

        $this->assertEquals($methods, $nc->getObjectActions());
        $this->assertTrue($nc->hasObjectAction('doThisKey'));
        $this->assertFalse($nc->hasObjectAction('doAnything'));
    }

    /**
     * @expectedException NakedPhp\ProgModel\Exception
     */
    public function testDoesNotAllowMultipleInitializationOfActions()
    {
        $nc = new PhpSpecification(null, array('doThisKey' => 'doThis'));
        $nc->initObjectActions(array('doThatKey' => 'doThat'));
    }

    public function testActionsListIsInitializedByDefaultToEmpty()
    {
        $nc = new PhpSpecification();
        $this->assertEquals(array(), $nc->getObjectActions());
    }

    public function testGivesAccessToAnActionByName()
    {
        $nc = new PhpSpecification('', array('key' => 'doThis', 'doThat'));
        $this->assertEquals('doThis', $nc->getObjectAction('key'));
    }

    public function testImplementsFacetHolderInterface()
    {
        $nc = new PhpSpecification();
        $helper = new \NakedPhp\Test\FacetHolder($this);
        $helper->testIsFacetHolder($nc);
    }

    public function testAllowsInitializationOfServiceMarkAsFalse()
    {
        $nc = new PhpSpecification();
        $nc->markAsEntity();
        $this->assertFalse($nc->isService());
    }

    public function testAllowsInitializationOfServiceMarkAsTrue()
    {
        $nc = new PhpSpecification();
        $nc->markAsService();
        $this->assertTrue($nc->isService());
    }

    /**
     * @expectedException NakedPhp\ProgModel\Exception
     */
    public function testDoesNotAllowMultipleInitializationOfServiceMark()
    {
        $nc = new PhpSpecification();
        $nc->markAsService();
        $nc->markAsEntity();
    }

    public function testRetainsFieldsList()
    {
        $nc = new PhpSpecification(null, null, $fields = array('Name', 'Role'));
        $this->assertEquals($fields, $nc->getAssociations());
    }

    public function testAllowsInitializationOfTheAssociationsList()
    {
        $nc = new PhpSpecification(null, null, null);
        $nc->initAssociations($fields = array('Name', 'Role'));
        $this->assertEquals($fields, $nc->getAssociations());
    }

    /**
     * @expectedException NakedPhp\ProgModel\Exception
     */
    public function testDoesNotAllowMultipleInitializationOfAssociations()
    {
        $nc = new PhpSpecification(null, null, array('Status'));
        $nc->initAssociations($fields = array('Name', 'Role'));
    }

    public function testGivesAccessToAFieldByName()
    {
        $nc = new PhpSpecification('', array(), array('key' => 'Name', 'Role'));
        $this->assertEquals('Name', $nc->getAssociation('key'));
    }
}
