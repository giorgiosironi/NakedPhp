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

namespace NakedPhp\Reflect\FacetFactory;
use NakedPhp\Reflect\MethodRemover;
use NakedPhp\MetaModel\Facet;
use NakedPhp\MetaModel\NakedObjectFeatureType;
use NakedPhp\Stubs\FacetHolderStub;

class ActionMethodsFacetFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $_facetFactory;
    private $_reflectionClass;

    public function setUp()
    {
        $this->_facetFactory = new ActionMethodsFacetFactory();
        $this->_reflectionClass = new \ReflectionClass('NakedPhp\Reflect\FacetFactory\SomeRandomClass');
    }

    public function testIsAppropriateForSomeFeatureType()
    {
        $this->assertEquals(array(NakedObjectFeatureType::ACTION),
                            $this->_facetFactory->getFeatureTypes());
    }
}

class SomeRandomClass
{
    public function doSomething() {}
}
