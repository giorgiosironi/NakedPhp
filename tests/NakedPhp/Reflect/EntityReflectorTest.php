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
use NakedPhp\Metadata\NakedEntitySpecification;
use NakedPhp\Metadata\OneToOneAssociation;
use NakedPhp\Metadata\NakedObjectAction;
use NakedPhp\Metadata\Facet;
use NakedPhp\Metadata\Facet\Action\Invocation;

class EntityReflectorTest extends \PHPUnit_Framework_TestCase
{
    private $_methodsReflector;
    private $_reflector;

    public function setUp()
    {
        $this->_methodsReflector = $this->getMock('NakedPhp\Reflect\MethodsReflector', array('analyze'));
        $methods = array(
            'sendMessage' => new NakedObjectAction('sendMessage'),
            'getName' => new NakedObjectAction('getName'),
            'setName' => new NakedObjectAction('setName'),
            'getStatus' => new NakedObjectAction('getStatus', array(), 'string'),
            'choicesStatus' => new NakedObjectAction('choicesStatus', array(), 'array'),
            'disableStatus' => new NakedObjectAction('disableStatus', array(), 'boolean'),
            'validateStatus' => new NakedObjectAction('validateStatus', array(), 'boolean'),
            'hideStatus' => new NakedObjectAction('hideStatus', array(), 'boolean')
        );
        $this->_methodsReflector->expects($this->once())
                                ->method('analyze')
                                ->will($this->returnValue($methods));

        $this->_reflector = new EntityReflector($this->_methodsReflector);
    }

    public function testCreatesAClassMetadataObject()
    {
        $result = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $this->assertTrue($result instanceof NakedEntitySpecification);
    }

    /**
     * TODO: refactor in FacetFactory implementations
     */
    public function testListsBusinessMethodsOfAnEntityObjectAsFacets()
    {
        $class = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $facet = $class->getMethod('sendMessage')->getFacet('Action\Invocation');
        $this->assertTrue($facet instanceof Invocation);
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
        $this->assertTrue($fields['status'] instanceof OneToOneAssociation);
        $this->assertEquals('status', $fields['status']->getId());
        $this->assertEquals('string', $fields['status']->getType());
    }

    /**
     * TODO: refactor in FacetFactory implementations
     */
    public function testGenerateFacetsForChoices()
    {
        $class = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $field = $class->getField('status');
        $facet = $field->getFacet('Property\Choices');
        $this->assertTrue($facet instanceof Facet);
    }

    /**
     * TODO: refactor in FacetFactory implementations
     */
    public function testGenerateFacetsForDisabledFeatures()
    {
        $class = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $field = $class->getField('status');
        $facet = $field->getFacet('Disabled');
        $this->assertTrue($facet instanceof Facet);
    }

    /**
     * TODO: refactor in FacetFactory implementations
     */
    public function testGenerateFacetsForValidation()
    {
        $class = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $field = $class->getField('status');
        $facet = $field->getFacet('Property\Validate');
        $this->assertTrue($facet instanceof Facet);
    }

    /**
     * TODO: refactor in FacetFactory implementations
     */
    public function testGenerateFacetsForHiding()
    {
        $class = $this->_reflector->analyze('NakedPhp\Stubs\User');
        $field = $class->getField('status');
        $facet = $field->getFacet('Hidden');
        $this->assertTrue($facet instanceof Facet);
    }
}
