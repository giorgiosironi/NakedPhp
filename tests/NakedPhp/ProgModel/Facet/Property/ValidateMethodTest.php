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

namespace NakedPhp\ProgModel\Facet\Property;
use NakedPhp\ProgModel\NakedBareObject;

class ValidateMethodTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsRightFacetType()
    {
        $facet = new ValidateMethod('validateMyProperty');
        $this->assertEquals('Property\Validate', $facet->facetType());
    }

    public function testReturnsValidStateEstablishedFromTheHookMethod()
    {
        $no = new NakedBareObject($this);
        $facet = new ValidateMethod('validateMyProperty');
        $valid = $facet->invalidReason($no, 42);
        $this->assertFalse($valid);
    }
    
    public function validateMyProperty($proposed)
    {
        $this->assertEquals(42, $proposed);
        return false;
    }
}
