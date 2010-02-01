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
 * @package    NakedPhp_Test
 */

namespace NakedPhp\Test;
use NakedPhp\Stubs\DummyFacet;

class FacetHolder
{
    private $_test;

    public function __construct(\PHPUnit_Framework_TestCase $test)
    {
        $this->_test = $test;
    }

    public function testIsFacetHolder($object)
    {
        $this->_test->assertTrue($object instanceof \NakedPhp\MetaModel\FacetHolder);

        $facet = new DummyFacet();
        $object->addFacet($facet);
        $this->_test->assertSame($facet, $object->getFacet('DummyFacet'));
        $this->_test->assertNull($object->getFacet('Property\NotExistent'));

        $object->addFacet(new DummyFacet());
        $this->_test->assertEquals(2, count($object->getFacets('DummyFacet')));
    }
}
