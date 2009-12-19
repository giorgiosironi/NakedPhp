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
 * @package    NakedPhp_Test
 */

namespace NakedPhp\Test;

/**
 * Simplify unit testing of method delegations
 */
class Delegation
{
    private $_original;
    
    /**
     * @param PHPUnit_Framework_TestCase
     * @param object $originalMock  mock of the object methods are delegated to
     */
    public function __construct(\PHPUnit_Framework_TestCase $test, $originalMock)
    {
        $this->_test = $test;
        $this->_original = $originalMock;
    }

    public function getterIs($method, $value)
    {
         $this->_original->expects($this->_test->once())
                         ->method($method)
                         ->will($this->_test->returnValue($value));
    }

    public function setterIs($method, $value)
    {
        $this->_original->expects($this->_test->once())
                        ->method($method)
                        ->with($value);
    }
}
