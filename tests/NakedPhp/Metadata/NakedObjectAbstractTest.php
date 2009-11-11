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

class NakedObjectAbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testIsADecoratorForTheDomainObject()
    {
        $no = new NakedObjectAbstract($this, null);
        $this->assertEquals('cannedResponse', $no->dummyMethod());
    }

    /**
     * @expectedException NakedPhp\Metadata\Exception
     */
    public function testRaiseExceptionWhenUnexistentMethodIsCalled()
    {
        $no = new NakedObjectAbstract($this, null);
        $no->foobar();
    }

    public function testDelegatesGettingClassNameOfTheDomainObject()
    {
        $no = new NakedObjectAbstract($this, new NakedClass('FooClass'));
        $this->assertEquals('FooClass', $no->getClassName());
    }

    public function testReturnsACommonStringRepresentationForUnconvertibleObjects()
    {
        $no = new NakedObjectAbstract($this, null);
        $this->assertEquals('OBJECT', (string) $no);
    }

    public function testIsEqualToAnotherOneWhichWrapTheSameObject()
    {
        $no = new NakedObjectAbstract($this, null);
        $another = new NakedObjectAbstract($this, null);
        $this->assertTrue($no->equals($another));
    }

    public function testIsNotEqualToAnotherOneWhichDoesNotWrapTheSameObject()
    {
        $no = new NakedObjectAbstract($this, null);
        $another = new NakedObjectAbstract(new \stdClass, null);
        $this->assertFalse($no->equals($another));
    }

    public function testContainsItsWrappedObject()
    {
        $no = new NakedObjectAbstract($this, null);
        $this->assertFalse($no->isWrapping(new \stdClass));
        $this->assertTrue($no->isWrapping($this));
    }

    /** self-shunting */
    public function dummyMethod()
    {
        return 'cannedResponse';
    }
}
