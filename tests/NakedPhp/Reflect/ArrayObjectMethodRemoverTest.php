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

class ArrayObjectMethodRemoverTest extends \PHPUnit_Framework_TestCase
{
    public function testRemovesMethodsWithAGivenPrefix()
    {
        $rc = new \ReflectionClass('NakedPhp\Reflect\ExampleClass');
        $methods = new \ArrayObject($rc->getMethods());
        $remover = new ArrayObjectMethodRemover($methods);
        $remover->removeMethods('get');
        $this->assertFalse($this->_inArray('getField', $methods));
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
