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
use NakedPhp\Metadata\NakedServiceClass;

class ServiceReflectorTest extends \PHPUnit_Framework_TestCase
{
    private $_reflector;
    private $_result;

    public function setUp()
    {
        $parserMock = $this->getMock('NakedPhp\Reflect\DocblockParser', array('parse'));
        $parserMock->expects($this->any())
                   ->method('parse')
                   ->will($this->returnValue(array()));
        $this->_reflector = new ServiceReflector($parserMock);
        $this->_result = $this->_reflector->analyze('NakedPhp\Stubs\UserFactory');
    }

    public function testRecognizesAnAnnotatedClass()
    {
        $parserMock = $this->getMock('NakedPhp\Reflect\DocblockParser', array('contains'), array(), '', false, false, false);
        $parserMock->expects($this->any())
                   ->method('contains')
                   ->will($this->returnValue(true));
        $this->_reflector = new ServiceReflector($parserMock);
        $this->assertTrue($this->_reflector->isService('NakedPhp\Stubs\UserFactory'));
    }

    public function testCreatesAClassMetadataObject()
    {
        $this->assertTrue($this->_result instanceof NakedServiceClass);
    }

    public function testListBusinessMethodsOfAnEntityObject()
    {
        $methods = $this->_result->getMethods();
        $this->assertEquals('createUser', (string) current($methods));
        $this->assertTrue(isset($methods['createUser']));
    }

    public function testDoesNotListMagicMethods()
    {
        $methods = $this->_result->getMethods();
        $this->assertFalse(isset($methods['__toString']));
    }
}
