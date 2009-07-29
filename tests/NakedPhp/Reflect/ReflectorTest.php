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
use NakedPhp\Metadata\NakedClass;

class ReflectorTest extends \PHPUnit_Framework_TestCase
{
    private $_reflector;
    private $_result;

    public function setUp()
    {
        // TRICKY: activate autoload
        new DocblockParser();
        $parserMock = $this->getMock('NakedPhp\Reflect\DocblockParser', array('parse'), array(), '', false, false, false);
        $parserMock->expects($this->any())
                   ->method('parse')
                   ->will($this->returnValue(array()));
        $this->_reflector = new EntityReflector($parserMock);
        $this->_result = $this->_reflector->analyze('NakedPhp\Stubs\User');
    }

    public function testCreatesAClassMetadataObject()
    {
        $this->assertTrue($this->_result instanceof NakedClass);
    }

    public function testListBusinessMethodsOfAnEntityObject()
    {
        $methods = $this->_result->getMethods();
        $this->assertEquals('sendMessage', (string) $methods[0]);
        $this->assertEquals(3, count($methods));
    }

    public function testListFieldsOfAnEntityObjectThatHaveSetterAndGetter()
    {
        $fields = $this->_result->getFields();
        $this->assertEquals('name', (string) $fields[0]);
    }

    public function testListFieldsOfAnEntityObjectThatHaveGetter()
    {
        $fields = $this->_result->getFields();
        $this->assertEquals('status', (string) $fields[1]);
    }
}
