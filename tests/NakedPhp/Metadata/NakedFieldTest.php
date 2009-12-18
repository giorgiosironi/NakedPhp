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

class NakedFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testRetainsTypeAndName()
    {
        $field = new NakedField('string', 'name');
        $this->assertEquals('string', (string) $field->getType());
        $this->assertEquals('name', $field->getName());
    }

    public function testImplementsFacetHolderInterface()
    {
        $field = new NakedField();
        $this->assertTrue($field instanceof FacetHolder);

        $dummy = new DummyFacet();
        $field->addFacet($dummy);
        $this->assertEquals($dummy, $field->getFacet('DummyFacet'));
        $this->assertNull($field->getFacet('NotExistent'));
    }

    /*
    public function testIsNotDefaultByDefault()
    {
        $param = new NakedParam('array', 'info');
        $this->assertFalse($param->getDefault());
    }
    */
}
