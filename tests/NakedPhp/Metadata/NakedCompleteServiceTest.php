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
use NakedPhp\Service\MethodMerger;
use NakedPhp\Stubs\DummyFacet;

class NakedCompleteServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testDelegatesToWrappedServiceForClassMetadata()
    {
        $bareNo = new NakedBareService($this, $class = new NakedServiceClass('FooClass', array('doSomething')));
        $no = new NakedCompleteService($bareNo);
        $this->assertSame($class, $no->getClass());
        $this->assertEquals('FooClass', $no->getClassName());
        $this->assertEquals('OBJECT', (string) $no);
    }

    public function testDelegatesMethodCallingToMethodCaller()
    {
        $bareNo = $this->getMock('NakedPhp\Metadata\NakedBareService');
        $merger = $this->getMock('NakedPhp\Service\MethodMerger');
        $merger->expects($this->once())
             ->method('call')
             ->with($bareNo, 'methodName', array('foo', 'bar'))
             ->will($this->returnValue('dummy'));
        $no = new NakedCompleteService($bareNo, $merger);

        $result = $no->__call('methodName', array('foo', 'bar'));

        $this->assertSame('dummy', $result);
    }

    public function testDelegatesToWrappedServiceForMethodsMetadata()
    {
        $bareNo = new NakedBareService($this, $class = new NakedServiceClass('', array('key' => 'doSomething')));
        $no = new NakedCompleteService($bareNo);
        $this->assertEquals(array('key' => 'doSomething'), $no->getMethods());
        $this->assertEquals('doSomething', $no->getMethod('key'));
        $this->assertTrue($no->hasMethod('key'));
        $this->assertFalse($no->hasMethod('not_existent_key'));
    }

    public function testProxiesToBareEntityForFacetHolding()
    {
        $class = new NakedServiceClass();
        $class->addFacet(new DummyFacet());
        $bareNo = new NakedBareService(null, $class);

        $no = new NakedCompleteService($bareNo);
        $this->assertNotNull($no->getFacet('DummyFacet'));
    }
}
