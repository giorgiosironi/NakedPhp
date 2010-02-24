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

namespace NakedPhp\Reflect\SpecificationFactory;
use NakedPhp\ProgModel\PhpSpecification;

class PhpTypesSpecificationFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $_specifications;

    public function setUp()
    {
        $specFactory = new PhpTypesSpecificationFactory();
        $this->_specifications = $specFactory->getSpecifications();
    }

    /**
     * Should create boolean, integer, float, string and array
     */
    public function testCreatesPhpSpecificationObjectsGivenSomeClassNames()
    {
        $this->assertEquals(5, count($this->_specifications));
        $spec = $this->_specifications['string'];
        $this->assertEquals('string', $spec->getClassName());
    }

    /**
     * @expectedException NakedPhp\ProgModel\Exception
     */
    public function testCreatesPhpSpecificationsWhoseAssociationsCannotBeSet()
    {
        $spec = current($this->_specifications);
        $spec->initAssociations(array());
    }

    /**
     * @expectedException NakedPhp\ProgModel\Exception
     */
    public function testCreatesPhpSpecificationsWhoseActionsCannotBeSet()
    {
        $spec = current($this->_specifications);
        $spec->initObjectActions(array());
    }
}