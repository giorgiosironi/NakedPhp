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
 * @package    NakedPhp_Reflect
 */

namespace NakedPhp\Reflect;
use NakedPhp\ProgModel\PhpSpecification;

/**
 * PhpIntrospector must accomplish:
 * - introspection of class
 * - introspection of associations
 * - introspection of action method
 */
class PhpIntrospectorTest extends \PHPUnit_Framework_TestCase
{
    private $_specification;
    private $_facetProcessor;
    private $_introspector;

    public function setUp()
    {
        $this->_specification = new PhpSpecification('NakedPhp\Reflect\DummyClass');
        $this->_facetProcessor = $this->getMock('NakedPhp\Reflect\FacetProcessor');
        $this->_introspector = new PhpIntrospector($this->_specification,
                                                   $this->_facetProcessor);
    }

    public function testIntrospectsClass()
    {
        $this->_facetProcessor->expects($this->once())
                              ->method('processClass')
                              ->with($this->anything(), $this->anything(), $this->_specification);
        $this->_facetProcessor->expects($this->exactly(3))
                              ->method('processMethod')
                              ->with($this->anything(), $this->anything(), $this->anything(), $this->_specification);

        $this->_introspector->introspectClass();
    }

    public function testIntrospectsAssociations()
    {
        $rc = new \ReflectionClass('NakedPhp\Reflect\DummyClass');
        $methods = array($rc->getMethod('foo1'), $rc->getMethod('foo2'));
        $this->_facetProcessor->expects($this->once())
                              ->method('removePropertyAccessors')
                              ->will($this->returnValue($methods));

        $this->_facetProcessor->expects($this->exactly(2))
                              ->method('processClass')
                              ->with($this->anything(), $this->anything(), $this->anything());

        $this->_facetProcessor->expects($this->exactly(2))
                              ->method('processMethod')
                              ->with($this->anything(), $this->anything(), $this->anything(), $this->anything());

        $this->_introspector->introspectAssociations();
    }
}

class DummyClass
{
    public function foo1() {}
    public function foo2() {}
    public function foo3() {}
}
