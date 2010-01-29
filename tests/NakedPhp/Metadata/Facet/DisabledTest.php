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

namespace NakedPhp\Metadata\Facet;
use NakedPhp\Metadata\NakedBareObject;

class DisabledTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsRightFacetType()
    {
        $facet = new Disabled('myProperty');
        $this->assertEquals('Disabled', $facet->facetType());
    }

    public function testReturnsReasonEstablishedFromTheHookMethod()
    {
        $no = new NakedBareObject($this);
        $facet = new Disabled('myProperty');
        $reason = $facet->disabledReason($no);
        $this->assertEquals('Not available.', $reason);
    }
    
    public function disableMyProperty()
    {
        return 'Not available.';
    }
}
