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
 * @package    NakedPhp_ProgModel
 */

namespace NakedPhp\ProgModel;
use NakedPhp\Stubs\NakedObjectSpecificationStub;
use NakedPhp\Test\TestCase;

/**
 * Implementation of NakedObject (Decorators too) should have a test case that extends this class.
 * They should define the $_object and $_delegation members. $_object should wrap $this.
 */
abstract class AbstractNakedObjectTest extends TestCase
{
    /** @var NakedObject */
    protected $_object;
    /** @var NakedPhp\Test\Delegation */
    protected $_delegation;

    abstract protected function _loadDelegation();

    public function setUp()
    {
        $this->_loadDelegation();
    }

    public function testDelegatesAccessToClassNameToTheInnerSpecification()
    {
        $this->_delegation->getterIs('getClassName', 'FooClass');
        $this->assertEquals('FooClass', $this->_object->getClassName());
    }

    public function testDelegatesTypeAccessToTheInnerSpecification()
    {
        $this->_delegation->getterIs('isService', 'aBoolean');
        $this->assertEquals('aBoolean', $this->_object->isService());
    }

    public function testDelegatesFieldsListToTheInnerSpecification()
    {
        $this->_delegation->getterIs('getFields', $expected = array('name' => 'Name'));
        $this->assertSame($expected, $this->_object->getFields());
    }

    public function testDelegatesSingleFieldAccessToTheInnerSpecification()
    {
        $this->_delegation->getterIs('getField', 'Name');
        $this->assertSame('Name', $this->_object->getField('name'));
    }

}
