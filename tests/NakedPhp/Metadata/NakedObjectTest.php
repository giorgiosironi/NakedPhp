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
 * @package    NakedPhp_Metadata
 */

namespace NakedPhp\Metadata;

class NakedObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testIsADecoratorForTheDomainObject()
    {
        $no = new NakedObject($this, null);
        $this->assertEquals('cannedResponse', $no->dummyMethod());
    }

    /**
     * @expectedException NakedPhp\Metadata\Exception
     */
    public function testRaiseExceptionWhenUnexistentMethodIsCalled()
    {
        $no = new NakedObject($this, null);
        $no->foobar();
    }

    public function testDiscoversClassNameOfTheDomainObject()
    {
        $no = new NakedObject($this, null);
        $this->assertEquals('NakedPhp\Metadata\NakedObjectTest', $no->getClassName());
    }

    public function testReturnsACommonStringRepresentationForUnconvertibleObjects()
    {
        $no = new NakedObject($this, null);
        $this->assertEquals('OBJECT', (string) $no);
    }

    public function testIsEqualToAnotherOneWhichWrapTheSameObject()
    {
        $no = new NakedObject($this, null);
        $another = new NakedObject($this, null);
        $this->assertTrue($no->equals($another));
    }

    public function testIsNotEqualToAnotherOneWhichDoesNotWrapTheSameObject()
    {
        $no = new NakedObject($this, null);
        $another = new NakedObject(new \stdClass, null);
        $this->assertFalse($no->equals($another));
    }

    /** self-shunting */
    public function dummyMethod()
    {
        return 'cannedResponse';
    }
}
