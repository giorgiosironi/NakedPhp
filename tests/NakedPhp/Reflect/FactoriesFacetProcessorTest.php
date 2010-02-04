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

}
