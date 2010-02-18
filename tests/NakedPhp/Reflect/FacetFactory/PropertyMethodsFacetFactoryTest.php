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

class PropertyMethodsFacetFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $_facetFactory;
    private $_reflectionClass;

    public function setUp()
    {
        $this->_facetFactory = new PropertyMethodsFacetFactory();
        $this->_reflectionClass = new \ReflectionClass('NakedPhp\Reflect\FacetFactory\SomeRandomEntityClass');
    }

    public function testIsAppropriateForSomeFeatureType()
    {
        $this->assertEquals(array(NakedObjectFeatureType::PROPERTY),
                            $this->_facetFactory->getFeatureTypes());
    }

    public function testFindsTheAccessorCandidatesInMethodThatStartWithGet()
    {
        $removerMock = $this->_getMethodRemoverMock();
        $removerMock->expects($this->once())
                    ->method('removeMethods')
                    ->with('get')
                    ->will($this->returnValue('dummy'));
        $methods = $this->_facetFactory->removePropertyAccessors($removerMock);
        $this->assertEquals('dummy', $methods);
    }

    public function testRecognizesGetters()
    {
        $getter = $this->_reflectionClass->getMethod('getBar');
        $this->assertTrue($this->_facetFactory->recognizes($getter));
    }
 
    public function testRecognizesSetters()
    {
        $setter = $this->_reflectionClass->getMethod('setBar');
        $this->assertTrue($this->_facetFactory->recognizes($setter));
    }
   
    public function testAddsReadOnlyPropertyFacetIfSetterIsNotPresent()
    {
        $getter = $this->_reflectionClass->getMethod('getFoo');
        $facetHolder = $this->_processGetter($getter);
        $this->assertNull($facetHolder->getFacet('Property\Setter'));
    }

    public function testAddsThePropertySetterFacet()
    {
        $getter = $this->_reflectionClass->getMethod('getBar');
        $facetHolder = $this->_processGetter($getter);
        $this->assertNotNull($facetHolder->getFacet('Property\Setter'));
    }

    public function testGenerateFacetsForChoices()
    {
        $getter = $this->_reflectionClass->getMethod('getStatus');
        $facetHolder = $this->_processGetter($getter);
        $facet = $facetHolder->getFacet('Property\Choices');
        $this->assertTrue($facet instanceof Facet);
    }

    public function testGenerateFacetsForDisabledFeatures()
    {
        $getter = $this->_reflectionClass->getMethod('getPassword');
        $facetHolder = $this->_processGetter($getter);
        $facet = $facetHolder->getFacet('Disabled');
        $this->assertTrue($facet instanceof Facet);
    }

    public function testGenerateFacetsForValidation()
    {
        $getter = $this->_reflectionClass->getMethod('getEmail');
        $facetHolder = $this->_processGetter($getter);
        $facet = $facetHolder->getFacet('Property\Validate');
        $this->assertTrue($facet instanceof Facet);
    }

    public function testGenerateFacetsForHiding()
    {
        $getter = $this->_reflectionClass->getMethod('getId');
        $facetHolder = $this->_processGetter($getter);
        $facet = $facetHolder->getFacet('Hidden');
        $this->assertTrue($facet instanceof Facet);
    }

    private function _processGetter(\ReflectionMethod $method)
    {
        $removerMock = $this->_getMethodRemoverMock();
        $facetHolder = new FacetHolderStub();
        $this->_facetFactory->processMethod($this->_reflectionClass, $method, $removerMock, $facetHolder);
        return $facetHolder;
    }

    private function _getMethodRemoverMock()
    {
        return $this->getMock('NakedPhp\Stubs\DummyMethodRemover');
    }
}

class SomeRandomEntityClass
{
    public function getFoo() {}

    public function getBar() {}
    public function setBar() {}

    public function getStatus() {}
    public function choicesStatus() {}
    
    public function getPassword() {}
    public function disablePassword() {}

    public function getEmail() {}
    public function validateEmail() {}

    public function getId() {}
    public function hideId() {}
}
