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
 * @package    NakedPhp_Service
 */

namespace NakedPhp\Service;
use NakedPhp\Metadata\NakedObject;
use NakedPhp\Metadata\NakedEntityClass;
use NakedPhp\Metadata\NakedMethod;

class MethodMergerTest extends \PHPUnit_Framework_TestCase
{
    public function testCallsAMethodOfTheObjectClass()
    {
        $methodCaller = new MethodMerger();
        new \NakedPhp\Stubs\User();
        $mock = $this->getMock('NakedPhp\Stubs\User', array('sendMessage'), array(), '', false, false, false);
        $mock->expects($this->once())
             ->method('sendMessage')
             ->with('Title', 'text...');
        $methodCaller->call(new NakedObject($mock), 'sendMessage', array('Title', 'text...'));
    }

    public function testListsMethodOfTheObjectClass()
    {
        $mock = $this->getMock('NakedPhp\Service\ServiceProvider', array('getServiceClasses', 'getService'), array(), '', false, false, false);
        $mock->expects($this->any())
             ->method('getServiceClasses')
             ->will($this->returnValue(array()));
        $methodCaller = new MethodMerger($mock);
        $class = new NakedEntityClass(array('doSomething' => new NakedMethod('doSomething')));
        $methods = $methodCaller->getApplicableMethods($class);
        $this->assertEquals(array('doSomething'), array_keys($methods));
    }
}
