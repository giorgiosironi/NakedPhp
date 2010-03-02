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

namespace NakedPhp\Reflect\Introspector;

class ArrayObjectMethodRemoverTest extends \PHPUnit_Framework_TestCase
{
    private $_methods;
    private $_remover;

    public function setUp()
    {
        $rc = new \ReflectionClass('NakedPhp\Reflect\Introspector\ExampleClass');
        $this->_methods = new \ArrayObject($rc->getMethods());
        $this->_remover = new ArrayObjectMethodRemover($this->_methods);
    }

    public function testRemovesMethodsWithAGivenPrefix()
    {
        $this->_remover->removeMethods('get');
        $this->assertFalse($this->_inArray('getField', $this->_methods));
    }

    public function testReturnsMethodsWithAGivenPrefix()
    {
        $methods = $this->_remover->removeMethods('get');
        $this->assertTrue($this->_inArray('getField', $methods));
    }

    private function _inArray($methodName, $methods)
    {
        foreach ($methods as $method) {
            if ($method->getName() == $methodName) {
                return true;
            }
        }
        return false;
    }
}

class ExampleClass {
    public function getField() {}
    public function setField() {}
    public function foobar() {}
}
