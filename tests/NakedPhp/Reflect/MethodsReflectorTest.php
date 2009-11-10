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

class MethodsReflectorTest extends \PHPUnit_Framework_TestCase
{
    private $_parserMock;
    private $_reflector;

    public function setUp()
    {
        $this->_parserMock = $this->getMock('NakedPhp\Reflect\DocblockParser', array('parse', 'contains'));
        $this->_reflector = new MethodsReflector($this->_parserMock);
    }

    private function setMockAnnotations($annotations = null)
    {
        if (is_null($annotations)) {
            $annotations = array(
               array(
                   'annotation' => 'return',
                   'type' => 'integer',
                   'description' => 'The role of the user'
               )
           );
        }
        $this->_parserMock->expects($this->any())
                   ->method('parse')
                   ->will($this->returnValue($annotations));
    }

    public function testReturnsAnArrayOfMethods()
    {
        $this->setMockAnnotations();
        $methods = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $this->assertTrue(is_array($methods));
    }

    public function testListsBusinessMethodsOfAnEntityObject()
    {
        $this->setMockAnnotations();
        $methods = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $this->assertTrue(isset($methods['sendMessage']));
        $this->assertEquals('sendMessage', (string) $methods['sendMessage']);
    }

    public function testDoesNotListMagicMethods()
    {
        $this->setMockAnnotations();
        $methods = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $this->assertFalse(isset($methods['__toString']));
    }

    public function testExtractsReturnTypeAndParametersFromAnnotations()
    {
        $this->setMockAnnotations();
        $methods = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $this->assertEquals('integer', $methods['getStatus']->getReturn());
    }

    public function testAssumesTheDefaultParametersTypeAsStringIfNoAnnotationsAreDefined()
    {
        $this->setMockAnnotations(array());
        $methods = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $params = $methods['validatePhonenumber']->getParams();
        $this->assertEquals('string', $params['phonenumber']->getType());
    }

    public function testAssumesTheDefaultReturnTypeAsStringIfNoAnnotationsAreDefined()
    {
        $this->setMockAnnotations(array());
        $methods = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $return = $methods['validatePhonenumber']->getReturn();
        $this->assertEquals('string', $return);
    }

    public function testSkipsMethodsHiddenVoluntarily()
    {
        $this->setMockAnnotations();
        $this->_parserMock->expects($this->any())
                          ->method('contains')
                          ->with('Hidden', $this->anything())
                          ->will($this->returnValue(true));
        $methods = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $this->assertFalse(isset($methods['mySkippedMethod']));
    }
}
