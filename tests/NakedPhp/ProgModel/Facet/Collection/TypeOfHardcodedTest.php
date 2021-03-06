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

namespace NakedPhp\ProgModel\Facet\Collection;
use NakedPhp\Stubs\NakedObjectSpecificationStub;

class TypeOfHardcodedTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsRightFacetType()
    {
        $facet = new TypeOfHardcoded(null);
        $this->assertEquals('Collection\TypeOf', $facet->facetType());
    }

    public function testReturnsTheStoredSpecification()
    {
        $spec = new NakedObjectSpecificationStub();
        $facet = new TypeOfHardcoded($spec);
        $this->assertSame($spec, $facet->valueSpec());
    }
}
