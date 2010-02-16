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
use NakedPhp\Stubs\NakedObjectStub;
use NakedPhp\ProgModel\OneToOneAssociation;
use NakedPhp\ProgModel\Facet\DisabledMethod;
use NakedPhp\ProgModel\Facet\Property\ChoicesMethod;
use NakedPhp\ProgModel\Facet\Property\ValidateMethod;

class FieldsFormBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $_methodCaller;
    private $_formBuilder;
    private $_form;
    private $_fields;

    public function setUp()
    {
        $this->_formBuilder = new FieldsFormBuilder();
        $this->_fields = array(
            'first' => new OneToOneAssociation('string', 'first'),
            'second' => new OneToOneAssociation('integer', 'second'),
            'oneRelation' => new OneToOneAssociation('NakedPhp\Stubs\User', 'oneRelation'),
        );
    }

    private function _getForm()
    {
        $entity = new NakedObjectStub($this);
        return $this->_formBuilder->createForm($entity, $this->_fields);
    }

    public function testCreatesAZendForm()
    {
        $form = $this->_getForm();
        $this->assertTrue($form instanceof \Zend_Form);
    }

    public function testCreatesInputsForEveryFieldAndASubmit()
    {
        $form = $this->_getForm();
        $this->assertEquals(count($this->_fields) + 1, count($form));
    }

    public function testCreatesALabelForEveryInput()
    {
        $form = $this->_getForm();
        $this->assertEquals('first', $form->first->getLabel());
    }

    public function testCreatesSelectForOneTargetRelationships()
    {
        $form = $this->_getForm();
        $this->assertTrue($form->oneRelation instanceof \NakedPhp\Form\ObjectSelect);
    }

    public function testCreatesSelectForLimitedChoices()
    {
        $entity = new NakedObjectStub($this);
        $field = new OneToOneAssociation('string', 'limitedField');
        $field->addFacet(new ChoicesMethod('choicesLimitedField'));
        $element = $this->_formBuilder->createElement($entity, $field);
        $this->assertTrue($element instanceof \Zend_Form_Element_Select);
        $expected = array('foo' => 'Foo', 'bar' => 'Bar');
        $this->assertEquals($expected, $element->getMultiOptions());
    }

    public function choicesLimitedField()
    {
        return array('foo' => 'Foo', 'bar' => 'Bar');
    }

    public function testDisablesInputProgrammatically()
    {
        $entity = new NakedObjectStub($this);
        $field = new OneToOneAssociation('string', 'myField');
        $field->addFacet(new DisabledMethod('disableMyField'));
        $element = $this->_formBuilder->createElement($entity, $field);
        $this->assertEquals('disabled', $element->getAttrib('disabled'));
    }

    public function disableMyField()
    {
        return true;
    }

    public function testShowsTooltipOnDisabledInputs()
    {
        $entity = new NakedObjectStub($this);
        $field = new OneToOneAssociation('string', 'myOtherField');
        $field->addFacet(new DisabledMethod('disableMyOtherField'));
        $element = $this->_formBuilder->createElement($entity, $field);

        $decorators = $element->getDecorators();
        $tooltipDecoratorOptions = $decorators['Tooltip']->getOptions();
        $labelDecoratorOptions = $decorators['Zend_Form_Decorator_Label']->getOptions();
        $this->assertEquals('You cannot edit this.', $tooltipDecoratorOptions['title']);
        $this->assertEquals('You cannot edit this.', $labelDecoratorOptions['title']);
    }

    public function disableMyOtherField()
    {
        return 'You cannot edit this.';
    }

    public function testValidatesInputProgrammatically()
    {
        $entity = new NakedObjectStub($this);
        $field = new OneToOneAssociation('string', 'myField');
        $field->addFacet(new ValidateMethod('validateMyField'));
        $element = $this->_formBuilder->createElement($entity, $field);
        $this->assertFalse($element->isValid('foo'));
    }

    public function validateMyField()
    {
        return false;
    }

    public function testNormalizesClassNameForRelationships()
    {
        $form = $this->_getForm();
        $classAttribute = $form->oneRelation->getAttrib('class');
        $this->assertEquals('NakedPhp-Stubs-User', $classAttribute);
    }
}

