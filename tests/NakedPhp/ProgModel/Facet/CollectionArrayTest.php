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

namespace NakedPhp\ProgModel\Facet;
use NakedPhp\MetaModel\NakedObject;
use NakedPhp\ProgModel\Facet\Collection\TypeOfHardcoded;
use NakedPhp\ProgModel\NakedBareObject;
use NakedPhp\Stubs\NakedObjectSpecificationStub;

class CollectionArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsRightFacetType()
    {
        $facet = new CollectionArray();
        $this->assertEquals('Collection', $facet->facetType());
    }

    public function testCreatesIteratorForAWrappedArray()
    {
        $array = new NakedBareObject(array('A', 'B', 'C'));
        $itemsSpec = new NakedObjectSpecificationStub('string');
        $facet = new CollectionArray(new TypeOfHardcoded($itemsSpec));
        $iterator = $facet->iterator($array);
        $this->assertTrue($iterator->current() instanceof NakedObject);
        $this->assertEquals('A', $iterator->current()->getObject());
        $this->assertEquals($itemsSpec, $iterator->current()->getSpecification());
    }
}
