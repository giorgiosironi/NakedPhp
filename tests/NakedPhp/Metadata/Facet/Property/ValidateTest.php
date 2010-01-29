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

namespace NakedPhp\Metadata\Facet\Property;
use NakedPhp\Metadata\NakedBareObject;

class ValidateTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsRightFacetType()
    {
        $facet = new Validate('myProperty');
        $this->assertEquals('Property\Validate', $facet->facetType());
    }

    public function testReturnsValidStateEstablishedFromTheHookMethod()
    {
        $no = new NakedBareObject($this);
        $facet = new Validate('myProperty');
        $valid = $facet->invalidReason($no, 42);
        $this->assertFalse($valid);
    }
    
    public function validateMyProperty($proposed)
    {
        $this->assertEquals(42, $proposed);
        return false;
    }
}
