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

class NakedObjectSpecificationTest extends \PHPUnit_Framework_TestCase
{
    public function testRetainsClassName()
    {
        $nc = new NakedObjectSpecification('stdClass', array());
        $this->assertEquals('stdClass', $nc->getClassName());
    }

    public function testRetainsMethodsList()
    {
        $nc = new NakedObjectSpecification('', $methods = array('doThis' => 'doThis', 'doThat'));
        $this->assertEquals($methods, $nc->getMethods());
        $this->assertTrue($nc->hasMethod('doThis'));
        $this->assertFalse($nc->hasMethod('doAnything'));
    }

    public function testGivesAccessToAMethodByName()
    {
        $nc = new NakedObjectSpecification('', array('key' => 'doThis', 'doThat'));
        $this->assertEquals('doThis', $nc->getMethod('key'));
    }

    public function testImplementsFacetHolderInterface()
    {
        $nc = new NakedObjectSpecification();
        $helper = new \NakedPhp\Test\FacetHolder($this);
        $helper->testIsFacetHolder($nc);
    }
}
