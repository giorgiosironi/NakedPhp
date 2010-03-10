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
    private $_array;
    private $_facet;
    private $_itemsSpec;

    public function setUp()
    {
        $this->_arrayNo = new NakedBareObject(array('A', 'B', 'C'));
        $this->_itemsSpec = new NakedObjectSpecificationStub('string');
        $this->_facet = new CollectionArray(new TypeOfHardcoded($this->_itemsSpec));
    }

    public function testReturnsRightFacetType()
    {
        $this->assertEquals('Collection', $this->_facet->facetType());
    }

    public function testCreatesIteratorForAWrappedArray()
    {
        $iterator = $this->_facet->iterator($this->_arrayNo);
        $this->assertTrue($iterator->current() instanceof NakedObject);
        $this->assertEquals('A', $iterator->current()->getObject());
        $this->assertEquals($this->_itemsSpec, $iterator->current()->getSpecification());
    }

    public function testReturnsWrappedArray()
    {
        $array = $this->_facet->toArray($this->_arrayNo);
        $this->assertEquals(3, count($array));
        $firstElement = $array[0];
        $this->assertTrue($firstElement instanceof NakedObject);
        $this->assertEquals('A', $firstElement->getObject());
        $this->assertEquals($this->_itemsSpec, $firstElement->getSpecification());
    }
}
