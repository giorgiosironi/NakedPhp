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
use NakedPhp\MetaModel\NakedObjectFeatureType;
use NakedPhp\Stubs\FacetHolderStub;

class PropertyMethodsFacetFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_facetFactory = new PropertyMethodsFacetFactory();
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
        $rc = new \ReflectionClass('NakedPhp\Reflect\FacetFactory\SomeRandomEntityClass');
        $getter = $rc->getMethod('getBar');
        $this->assertTrue($this->_facetFactory->recognizes($getter));
    }
 
    public function testRecognizesSetters()
    {
        $rc = new \ReflectionClass('NakedPhp\Reflect\FacetFactory\SomeRandomEntityClass');
        $setter = $rc->getMethod('setBar');
        $this->assertTrue($this->_facetFactory->recognizes($setter));
    }
   
    public function testAddsReadOnlyPropertyFacetIfSetterIsNotPresent()
    {
        $rc = new \ReflectionClass('NakedPhp\Reflect\FacetFactory\SomeRandomEntityClass');
        $getter = $rc->getMethod('getFoo');
        $removerMock = $this->_getMethodRemoverMock();
        $facetHolder = new FacetHolderStub();

        $this->_facetFactory->processMethod($rc, $getter, $removerMock, $facetHolder);

        $this->assertNull($facetHolder->getFacet('Property\Setter'));
    }

    public function testAddsThePropertySetterFacet()
    {
        $rc = new \ReflectionClass('NakedPhp\Reflect\FacetFactory\SomeRandomEntityClass');
        $getter = $rc->getMethod('getBar');
        $removerMock = $this->_getMethodRemoverMock();
        $facetHolder = new FacetHolderStub();

        $this->_facetFactory->processMethod($rc, $getter, $removerMock, $facetHolder);

        $this->assertNotNull($facetHolder->getFacet('Property\Setter'));
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
}
