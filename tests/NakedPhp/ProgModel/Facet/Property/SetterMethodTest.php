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

class SetterMethodTest extends \PHPUnit_Framework_TestCase
{
    private $_value;

    public function testReturnsRightFacetType()
    {
        $facet = new SetterMethod('myProperty');
        $this->assertEquals('Property\Setter', $facet->facetType());
    }

    public function testSetsAScalarAsFieldValue()
    {
        $no = new NakedBareObject($this);
        $facet = new SetterMethod('myProperty');
        $facet->setProperty($no, new NakedBareObject('dummy'));
        $this->assertEquals('dummy', $this->_value);
    }
    
    public function setMyProperty($value)
    {
        $this->_value = $value;
    }
}
