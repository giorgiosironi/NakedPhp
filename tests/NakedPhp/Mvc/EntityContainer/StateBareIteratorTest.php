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
use NakedPhp\Stubs\NakedObjectStub;

class StateBareIteratorTest extends \PHPUnit_Framework_TestCase
{
    private $_originalObject;
    private $_entityContainer;

    public function setUp()
    {
        $this->_originalObject = new NakedObjectStub(new \stdClass);
        $bareContainer = new BareContainer();
        $expectedKey = $bareContainer->add($this->_originalObject, EntityContainer::STATE_NEW);
        $this->_entityContainer = new StateBareIterator($bareContainer);
    }
    
    public function testWrapsElementOnIterationReturningStateAndObject()
    {
        $count = 0;
        foreach ($this->_entityContainer as $key => $tuple) {
            $count++;
            $this->assertEquals(1, $key);
            $this->assertEquals($this->_originalObject, $tuple['object']);
            $this->assertEquals(EntityContainer::STATE_NEW, $tuple['state']);
        }
        $this->assertEquals(1, $count);
    }
}
