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
 * @package    NakedPhp_Reflect
 */

namespace NakedPhp\Reflect\Introspector;
use NakedPhp\MetaModel\NakedObjectFeatureType;
use NakedPhp\ProgModel\OneToOneAssociation;
use NakedPhp\ProgModel\PhpAction;
use NakedPhp\ProgModel\PhpSpecification;

class PhpTypeIntrospectorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_specification = new PhpSpecification('string', null, null);
        $this->_introspector = new PhpTypeIntrospector($this->_specification);
    }

    public function testHoldsSpecification()
    {
        $this->assertSame($this->_specification, $this->_introspector->getSpecification());
    }

    // no-op
    public function testIntrospectsClass()
    {
        $this->_introspector->introspectClass();
    }

    public function testIntrospectsAssociations()
    {
        $this->_introspector->introspectAssociations();

        $associations = $this->_specification->getAssociations();
        $this->assertEquals(array(), $associations);
    }

    public function testIntrospectsObjectActions()
    {
        $this->_introspector->introspectActions();

        $actions = $this->_specification->getObjectActions();
        $this->assertEquals(array(), $actions);
    }
}
