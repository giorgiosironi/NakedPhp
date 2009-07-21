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

    public function testRetainsClassMetadata()
    {
        $no = new NakedObject($this, $class = new NakedClass(array('name'), array()));
        $this->assertSame($class, $no->getClass());
    }

    public function dummyMethod()
    {
        return 'cannedResponse';
    }
}
