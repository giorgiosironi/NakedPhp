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

class NakedCompleteServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testProxiesToWrappedServiceForClassMetadata()
    {
        $bareNo = new NakedBareService($this, $class = new NakedServiceClass('FooClass', array('doSomething')));
        $no = new NakedCompleteService($bareNo);
        $this->assertSame($class, $no->getClass());
        $this->assertEquals('FooClass', $no->getClassName());
    }

    /**
     * TODO: refactor this test together with NakedCompleteEntityTest
     *       to share wrapping of results and methodmerger delegation
     *       (extracting interface NakedFactory)
     */
    public function testAfterMethodCallingWrapsObjectResultUsingNakedFactory()
    {
        $this->fail();
        $expectedResult = new NakedBareEntity(new \stdClass);
        $factoryMock = $this->getMock('NakedPhp\Service\NakedFactory');
        $factoryMock->expects($this->any())
                           ->method('create')
                           ->will($this->returnValue($expectedResult));
        $bareNo = $this->getMock('NakedPhp\Metadata\NakedBareService');
        $merger = $this->_getMergerMock();
        $merger->expects($this->once())
             ->method('__call')
             ->with($bareNo, 'methodName')
             ->will($this->returnValue('dummy'));
        $no = new NakedCompleteEntity($bareNo, $merger, $factoryMock);

        $result = $no->__call('methodName');

        $this->assertSame($expectedResult, $result);
    }

    public function testProxiesToWrappedServiceForMethodsMetadata()
    {
        $bareNo = new NakedBareService($this, $class = new NakedServiceClass('', array('key' => 'doSomething')));
        $no = new NakedCompleteService($bareNo);
        $this->assertEquals(array('key' => 'doSomething'), $no->getMethods());
        $this->assertEquals('doSomething', $no->getMethod('key'));
        $this->assertTrue($no->hasMethod('key'));
        $this->assertFalse($no->hasMethod('not_existent_key'));
    }
}
