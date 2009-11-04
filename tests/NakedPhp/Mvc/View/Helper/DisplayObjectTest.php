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
 * @package    NakedPhp_Mvc
 */

namespace NakedPhp\Mvc\View\Helper;

class DisplayObjectTest extends \PHPUnit_Framework_TestCase
{
    private $_helper;
    private $_object;
    private $_result;

    public function setUp()
    {
        $this->_helper = new DisplayObject();
        $this->_object = $this->getMock('NakedPhp\Metadata\NakedBareEntity', array('getClassName', 'getState'), array(), '', false);
        $state = array(
            'firstName' => 'Giorgio',
            'lastName' => 'Sironi'
        );
        $this->_object->expects($this->any())
                      ->method('getClassName')
                      ->will($this->returnValue('DummyClass'));
        $this->_object->expects($this->any())
                      ->method('getState')
                      ->will($this->returnValue($state));
        $this->_result = $this->_helper->displayObject($this->_object);
    }

    public function testReturnsATable()
    {
        $this->assertQuery($this->_result, 'table.nakedphp_entity.DummyClass');
    }

    public function testFillsTableWithRowsBasingOnFields()
    {
        $this->assertQuery($this->_result, 'table tr');
    }

    public function testFillsCellsWithFieldsValues()
    {
        $this->assertQueryContentContains($this->_result, 'table tr td', 'firstName');
        $this->assertQueryContentContains($this->_result, 'table tr td', 'Giorgio');
        $this->assertQueryContentContains($this->_result, 'table tr td', 'lastName');
        $this->assertQueryContentContains($this->_result, 'table tr td', 'Sironi');
    }

    /**
     * Assert against DOM selection
     * 
     * @param  string $path CSS selector path
     * @param  string $message
     * @return void
     */
    public function assertQuery($content, $path, $message = '')
    {
        $constraint = new \Zend_Test_PHPUnit_Constraint_DomQuery($path);
        if (!$constraint->evaluate($content, __FUNCTION__)) {
            $constraint->fail($path, $message);
        }
    }

    /**
     * Assert against DOM selection; node should contain content
     * 
     * @param  string $path CSS selector path
     * @param  string $match content that should be contained in matched nodes
     * @param  string $message
     * @return void
     */
    public function assertQueryContentContains($content, $path, $match, $message = '')
    {
        $constraint = new \Zend_Test_PHPUnit_Constraint_DomQuery($path);
        if (!$constraint->evaluate($content, __FUNCTION__, $match)) {
            $constraint->fail($path, $message);
        }
    }
}
