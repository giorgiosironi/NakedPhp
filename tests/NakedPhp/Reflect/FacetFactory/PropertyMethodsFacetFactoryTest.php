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

class PropertyMethodsFacetFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAppropriateForSomeFeatureType()
    {
        $ff = new PropertyMethodsFacetFactory();
        $this->assertEquals(array(NakedObjectFeatureType::PROPERTY),
                            $ff->getFeatureTypes());
    }

    public function testFindsTheAccessorCandidates()
    {
        $ff = new PropertyMethodsFacetFactory();
        $removerMock = $this->getMock('NakedPhp\Reflect\FacetFactory\DummyMethodRemover');
        $removerMock->expects($this->once())
                    ->method('removeMethods')
                    ->with('get')
                    ->will($this->returnValue('dummy'));
        $methods = $ff->removePropertyAccessors($removerMock);
        $this->assertEquals('dummy', $methods);
    }
}

class DummyMethodRemover implements MethodRemover
{
    public function removeMethods($prefix) {}
}
