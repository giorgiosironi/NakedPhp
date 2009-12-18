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

class NakedClassTest extends \PHPUnit_Framework_TestCase
{
    public function testRetainsClassName()
    {
        $nc = new NakedClass('stdClass', array());
        $this->assertEquals('stdClass', $nc->getClassName());
    }

    public function testRetainsMethodsList()
    {
        $nc = new NakedClass('', $methods = array('doThis' => 'doThis', 'doThat'));
        $this->assertEquals($methods, $nc->getMethods());
        $this->assertTrue($nc->hasMethod('doThis'));
        $this->assertFalse($nc->hasMethod('doAnything'));
    }

    public function testGivesAccessToAMethodByName()
    {
        $nc = new NakedClass('', array('key' => 'doThis', 'doThat'));
        $this->assertEquals('doThis', $nc->getMethod('key'));
    }

    public function  testImplementsFacetHolderInterface()
    {
        $nc = new NakedClass();
        $this->assertTrue($nc instanceof FacetHolder);

        $facet = new DummyFacet();
        $nc->addFacet($facet);
        $this->assertSame($facet, $nc->getFacet('DummyFacet'));
        $this->assertNull($nc->getFacet('Property\NotExistent'));
    }
}
