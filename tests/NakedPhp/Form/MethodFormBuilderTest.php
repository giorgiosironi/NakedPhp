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
 * @package    NakedPhp_Form
 */

namespace NakedPhp\Form;
use NakedPhp\Metadata\NakedMethod;
use NakedPhp\Metadata\NakedParam;

class MethodFormBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $_formBuilder;
    private $_form;
    private $_method;
    private $_params;

    public function setUp()
    {
        $this->_formBuilder = new MethodFormBuilder();
        $this->_params = array(
                      'first' => new NakedParam('string', 'TheFirstIsAString'),
                      'second' => new NakedParam('integer', 'TheInteger')
        );
        $this->_method = new NakedMethod('doSomething', $this->_params, 'boolean');
        $this->_form = $this->_formBuilder->createForm($this->_method);
    }

    public function testCreatesAZendForm()
    {
        $this->assertTrue($this->_form instanceof \Zend_Form);
    }

    public function testCreatesInputsForEveryParameterAndASubmit()
    {
        $this->assertEquals(count($this->_params) + 1, count($this->_form));
    }
}

