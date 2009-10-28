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
use NakedPhp\Metadata\NakedField;

class EntityReflectorTest extends \PHPUnit_Framework_TestCase
{
    private $_parserMock;
    private $_reflector;
    private $_result;

    public function setUp()
    {
        $this->_parserMock = $this->getMock('NakedPhp\Reflect\DocblockParser', array('parse', 'contains'));
        $this->_parserMock->expects($this->any())
                   ->method('parse')
                   ->will($this->returnValue(array(
                       array(
                           'annotation' => 'return',
                           'type' => 'string',
                           'name' => 'status',
                           'description' => 'The role of the user'
                       )
                   )));
        $this->_reflector = new EntityReflector($this->_parserMock);
    }

    public function testCreatesAClassMetadataObject()
    {
        $result = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $this->assertTrue($result instanceof NakedClass);
    }

    public function testListsBusinessMethodsOfAnEntityObject()
    {
        $result = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $methods = $result->getMethods();
        $this->assertEquals('sendMessage', (string) current($methods));
        $this->assertTrue(isset($methods['sendMessage']));
    }

    public function testDoesNotListMagicMethods()
    {
        $result = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $methods = $result->getMethods();
        $this->assertFalse(isset($methods['__toString']));
    }

    public function testListsHiddenMethodsInASpecialList()
    {
        $result = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $hiddenMethods = $result->getHiddenMethods();
        $this->assertEquals('choicesStatus', (string) $hiddenMethods['choicesStatus']);
        $this->assertEquals('disableStatus', (string) $hiddenMethods['disableStatus']);
    }

    public function testSkipsMethodsHiddenVoluntarily()
    {
        $this->_parserMock->expects($this->any())
                          ->method('contains')
                          ->with('Hidden', $this->anything())
                          ->will($this->returnValue(true));
        $result = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $methods = $result->getMethods();
        $this->assertFalse(isset($methods['mySkippedMethod']));
    }

    public function testListsFieldsOfAnEntityObjectThatHaveSetterAndGetter()
    {
        $result = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $fields = $result->getFields();
        $this->assertTrue(isset($fields['name']));
    }

    public function testListsFieldsOfAnEntityObjectThatHaveGetter()
    {
        $result = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $fields = $result->getFields();
        $this->assertTrue(isset($fields['status']));
    }

    public function testGathersMetadataOnTheField()
    {
        $result = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $fields = $result->getFields();
        $this->assertTrue($fields['status'] instanceof NakedField);
        $this->assertEquals('status', $fields['status']->getName());
        $this->assertEquals('string', $fields['status']->getType());
    }

    public function testDoesNotListFieldsWhichGetterIsAnnotatedWithHidden()
    {
        $this->_parserMock->expects($this->any())
                          ->method('contains')
                          ->with('Hidden', $this->anything())
                          ->will($this->returnValue(true));
        $result = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $fields = $result->getFields();
        $this->assertEquals(0, count($fields));
    }
}
