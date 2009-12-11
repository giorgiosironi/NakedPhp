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
use NakedPhp\Metadata\NakedBareEntity;

class BareWrappingIteratorTest extends \PHPUnit_Framework_TestCase
{
    private $_originalObject;
    private $_entityContainer;

    public function setUp()
    {
        $wrappedContainer = new UnwrappedContainer();
        $factory = $this->getMock('NakedPhp\Service\NakedFactory');
        $factory->expects($this->any())
                ->method('createBare')
                ->will($this->returnCallback(array($this, 'factoryMethod')));
        $this->_originalObject = new \stdClass;
        $expectedKey = $wrappedContainer->add($this->_originalObject);
        $this->_entityContainer = new BareWrappingIterator($wrappedContainer, $factory);
    }
    
    public function factoryMethod()
    {
        return new NakedBareEntity('expectedEntity');
    }

    public function testWrapsElementOnIteration()
    {
        $count = 0;
        foreach ($this->_entityContainer as $key => $entity) {
            $count++;
            $this->assertEquals(1, $key);
            $this->assertEquals(new NakedBareEntity('expectedEntity'), $entity);
        }
        $this->assertEquals(1, $count);
    }
}
