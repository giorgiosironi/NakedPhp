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
use NakedPhp\Metadata\NakedField;

class FieldsFormBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $_formBuilder;
    private $_form;
    private $_fields;

    public function setUp()
    {
        $this->_formBuilder = new FieldsFormBuilder();
        $this->_fields = array(
            'first' => new NakedField('string', 'TheFirstIsAString'),
            'second' => new NakedField('integer', 'TheInteger')
        );
        $this->_form = $this->_formBuilder->createForm($this->_fields);
    }

    public function testCreatesAZendForm()
    {
        $this->assertTrue($this->_form instanceof \Zend_Form);
    }

    public function testCreatesInputsForEveryFieldAndASubmit()
    {
        $this->assertEquals(count($this->_fields) + 1, count($this->_form));
    }
}

