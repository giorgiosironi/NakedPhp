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
 * @package    NakedPhp_Mvc
 */

namespace NakedPhp\Mvc\EntityContainer;
use NakedPhp\Mvc\EntityContainer;
use NakedPhp\MetaModel\NakedFactory;

class StateBareWrappingIteratorTest extends \PHPUnit_Framework_TestCase
{
    private $_originalObject;
    private $_entityContainer;

    public function setUp()
    {
        $this->_originalObject = new \stdClass;
        $wrappedContainer = new UnwrappedContainer();
        $expectedKey = $wrappedContainer->add($this->_originalObject);

        $factory = new DummyIncrementalNakedFactory();
        $this->_entityContainer = new StateBareWrappingIterator($wrappedContainer, $factory);
    }
    
    public function testWrapsElementOnIterationReturningStateAndObject()
    {
        $count = 0;
        foreach ($this->_entityContainer as $key => $tuple) {
            $entity = $tuple['object'];
            $count++;
            $this->assertEquals(1, $key);
            // tricky: @see NakedFactoryStub
            $this->assertEquals($count, $tuple['object']);
            $this->assertEquals(EntityContainer::STATE_NEW, $tuple['state']);
        }
        $this->assertEquals(1, $count);
    }
}


