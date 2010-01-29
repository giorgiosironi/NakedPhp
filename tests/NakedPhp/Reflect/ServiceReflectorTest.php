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
use NakedPhp\Metadata\NakedServiceSpecification;
use NakedPhp\Metadata\NakedObjectAction;
use NakedPhp\Metadata\Facet\Action\Invocation;

class ServiceReflectorTest extends \PHPUnit_Framework_TestCase
{
    private $_parserMock;
    private $_methodsReflectorMock;
    private $_reflector;

    public function setUp()
    {
        $this->_parserMock = $this->getMock('NakedPhp\Reflect\DocblockParser', array('contains'));
        $this->_methodsReflectorMock = $this->getMock('NakedPhp\Reflect\MethodsReflector', array('analyze'));
        $methods = array(
            'createUser' => new NakedObjectAction('createUser', array(), 'UserClass')
        );
        $this->_methodsReflectorMock->expects($this->any())
                                    ->method('analyze')
                                    ->will($this->returnValue($methods));
        $this->_reflector = new ServiceReflector($this->_parserMock, $this->_methodsReflectorMock);
    }

    public function testRecognizesAnAnnotatedClass()
    {
        $this->_parserMock->expects($this->any())
                   ->method('contains')
                   ->will($this->returnValue(true));
        $this->_result = $this->_reflector->analyze('NakedPhp\Stubs\UserFactory');
        $this->assertTrue($this->_reflector->isService('NakedPhp\Stubs\UserFactory'));
    }

    public function testCreatesAClassMetadataObject()
    {
        $this->_result = $this->_reflector->analyze('NakedPhp\Stubs\UserFactory');
        $this->assertTrue($this->_result instanceof NakedServiceSpecification);
    }

    /**
     * TODO: refactor in FacetFactory implementations
     */
    public function testListsBusinessMethodsOfAServiceObjectAsFacets()
    {
        $class = $this->_reflector->analyze('NakedPhp\Stubs\UserFactory');
        $facet = $class->getObjectAction('createUser')->getFacet('Action\Invocation');
        $this->assertTrue($facet instanceof Invocation);
    }

    public function testListBusinessMethodsOfAServiceObject()
    {
        $this->_result = $this->_reflector->analyze('NakedPhp\Stubs\UserFactory');
        $methods = $this->_result->getObjectActions();
        $this->assertEquals('createUser', (string) current($methods));
        $this->assertTrue(isset($methods['createUser']));
    }
}
