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

class NakedBareServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testRetainsClassMetadata()
    {
        $no = new NakedBareService($this, $class = new NakedServiceSpecification('FooClass', array('doSomething')));
        $this->assertSame($class, $no->getClass());
        $this->assertEquals('FooClass', $no->getClassName());
    }

    public function testUnwrapsTheWrappedService()
    {
        $no = new NakedBareService($this);

        $this->assertSame($this, $no->getObject());
    }

    public function testProxiesToClassForMethodsMetadata()
    {
        $no = new NakedBareService($this, $class = new NakedServiceSpecification('', array('key' => 'doSomething')));
        $this->assertEquals(array('key' => 'doSomething'), $no->getMethods());
        $this->assertEquals('doSomething', $no->getMethod('key'));
        $this->assertTrue($no->hasMethod('key'));
        $this->assertFalse($no->hasMethod('not_existent_key'));
    }
}
