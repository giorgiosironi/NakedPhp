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
 * @package    NakedPhp_Stubs
 */

namespace NakedPhp\Stubs;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Interface
     */
    private $_view;

    public function setUp()
    {
        $this->_view = new View();
    }

    public function testAllowsVariableAssignment()
    {
        $this->_view->foo = 'bar';
        $this->assertEquals('bar', $this->_view->foo);
        $this->assertTrue(isset($this->_view->foo));
        unset($this->_view->foo);
        $this->assertFalse(isset($this->_view->foo));
    }

    public function testAllowsArbitraryHelperAssignment()
    {
        $mock = $this->getMock('Zend_View_Helper_HeadTitle', array('headTitle'));
        $mock->expects($this->once())
             ->method('headTitle')
             ->with('My page')
             ->will($this->returnValue('ok'));
        $this->_view->setHelper('HeadTitle', $mock);
        $result = $this->_view->headTitle('My page');
        $this->assertEquals('ok', $result);
    }
}
