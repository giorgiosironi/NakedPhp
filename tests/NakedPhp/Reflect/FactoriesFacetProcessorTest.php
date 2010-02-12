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
use NakedPhp\MetaModel\AssociationIdentifyingFacetFactory;
use NakedPhp\MetaModel\MethodFilteringFacetFactory;
use NakedPhp\Reflect\FacetFactory\AbstractFacetFactory;
use NakedPhp\Reflect\MethodRemover;
use NakedPhp\Stubs\DummyMethodRemover;
use NakedPhp\Stubs\FacetHolderStub;

class FactoriesFacetProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testPropagatesProcessCallsToAllFactories()
    {
        $ffMock = $this->getMock('NakedPhp\Reflect\FacetFactory\AbstractFacetFactory');
        $processor = new FactoriesFacetProcessor(array(
            $ffMock
        ));
        $rc = new \ReflectionClass('stdClass');
        $method = new \ReflectionMethod('ArrayIterator', 'current');
        $remover = new DummyMethodRemover();
        $holder = new FacetHolderStub();
        $ffMock->expects($this->once())
               ->method('processClass')
               ->with($rc, $remover, $holder);

        $processor->processClass($rc, $remover, $holder);

        $ffMock->expects($this->once())
               ->method('processMethod')
               ->with($rc, $method, $remover, $holder);
        $processor->processMethod($rc, $method, $remover, $holder);
    }

    public function testPropagatesRemoveAccessorCallsToAllAssociationIdentifyingFactories()
    {
        $processor = new FactoriesFacetProcessor(array(
            new DummyAssociationIdentifyingFactory(array(4 => 'A', 8 => 'B')),
            new DummyAssociationIdentifyingFactory(array(16 => 'C', 42 => 'D')),
            $this->getMock('NakedPhp\Reflect\FacetFactory\AbstractFacetFactory')
        ));
        
        $remover = new DummyMethodRemover();
        $expected = array(4 => 'A', 8 => 'B', 16 => 'C', 42 => 'D');
        $this->assertEquals($expected, $processor->removePropertyAccessors($remover));
    }

    public function testPropagatesRecognizesCallsToAllMethodFilteringFactories()
    {
        $processor = new FactoriesFacetProcessor(array(
            new DummyMethodFilteringFactory(array('myMethod')),
            new DummyMethodFilteringFactory(array('yourMethod'))
        ));
        
        $rc = new \ReflectionClass('NakedPhp\Reflect\DummyClassWithFilteredMethods');
        $this->assertTrue($processor->recognizes($rc->getMethod('myMethod')));
        $this->assertTrue($processor->recognizes($rc->getMethod('yourMethod')));
        $this->assertFalse($processor->recognizes($rc->getMethod('hisMethod')));
    }
}

class DummyAssociationIdentifyingFactory extends AbstractFacetFactory implements AssociationIdentifyingFacetFactory
{
    protected $_removedMethods;
    public function __construct($removedMethods)
    {
        $this->_removedMethods = $removedMethods;
    }

    public function removePropertyAccessors(MethodRemover $remover)
    {
        return $this->_removedMethods;
    }
}

class DummyMethodFilteringFactory extends AbstractFacetFactory implements MethodFilteringFacetFactory
{
    protected $_recognizedMethods;
    public function __construct($recognizedMethods)
    {
        $this->_recognizedMethods = $recognizedMethods;
    }

    public function recognizes(\ReflectionMethod $method)
    {
        return in_array($method->getName(), $this->_recognizedMethods);
    }
}

class DummyClassWithFilteredMethods
{
    public function myMethod() {}
    public function yourMethod() {}
    public function hisMethod() {}
}
