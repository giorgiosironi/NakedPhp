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
use NakedPhp\MetaModel\NakedObjectSpecification;
use NakedPhp\ProgModel\OneToOneAssociation;

class MethodsReflectorTest extends \PHPUnit_Framework_TestCase
{
    private $_parserMock;
    private $_reflector;

    public function setUp()
    {
        $this->_parserMock = $this->getMock('NakedPhp\Reflect\DocblockParser');
        $this->_reflector = new MethodsReflector($this->_parserMock);
        $this->_reflectionClass = new \ReflectionClass('NakedPhp\Reflect\DummyReflectedClass');
    }

    private function _setMockPhpdocAnnotations($annotations = null)
    {
        if (is_null($annotations)) {
            $annotations = array(
               array(
                   'annotation' => 'return',
                   'type' => 'integer',
                   'description' => 'The role of the user'
               )
           );
        }
        $this->_parserMock->expects($this->any())
                   ->method('getPhpdocAnnotations')
                   ->will($this->returnValue($annotations));
    }

    private function _setMockNakedPhpAnnotations($annotations = array())
    {
        $this->_parserMock->expects($this->once())
                          ->method('getNakedPhpAnnotations')
                          ->will($this->returnValue($annotations));
    }

    public function testFindsIdentifierForAction()
    {
        $method = $this->_reflectionClass->getMethod('myMethod');
        $identifier = $this->_reflector->getIdentifierForAction($method);
        $this->assertEquals('myMethod', $identifier);
    }

    public function testFindsIdentifierForAssociation()
    {
        $getter = $this->_reflectionClass->getMethod('getMyField');
        $identifier = $this->_reflector->getIdentifierForAssociation($getter);
        $this->assertEquals('myField', $identifier);
    }

    public function testFindsMethodReturnType()
    {
        $this->_setMockPhpdocAnnotations(array(
            array(
                'annotation' => 'return',
                'type' => 'integer',
                'description' => 'The role of the user'
            )
        ));
        $this->_setMockNakedPhpAnnotations(array());

        $method = $this->_reflectionClass->getMethod('getMyField');
        $type = $this->_reflector->getReturnType($method);
        $this->assertEquals('integer', $type['specificationName']);
    }

    public function testSetsMethodReturnTypeAsNullIfNoAnnotationCanBeFound()
    {
        $this->_setMockPhpdocAnnotations(array());
        $this->_setMockNakedPhpAnnotations(array());

        $method = $this->_reflectionClass->getMethod('getMyField');
        $type = $this->_reflector->getReturnType($method);
        $this->assertNull($type['specificationName']);
    }

    public function testRecognizesMethodTypeOfAnnotation()
    {
        $this->_setMockPhpdocAnnotations(array());
        $this->_setMockNakedPhpAnnotations(array(
            'TypeOf' => array(
                'stdClass'
            )
        ));

        $method = $this->_reflectionClass->getMethod('getMyField');
        $type = $this->_reflector->getReturnType($method);
        $this->assertEquals('stdClass', $type['typeOf']);
    }

    public function testFindsParametersTypeAndIdentifiers()
    {
        $this->_setMockPhpdocAnnotations(array(
            array(
                'annotation' => 'param',
                'type' => 'integer',
                'name' => 'myParameter',
                'description' => 'My useful parameter.'
            )
        ));

        $method = $this->_reflectionClass->getMethod('myMethod');
        $params = $this->_reflector->getParameters($method);
        $this->assertEquals(array(
                                'myParameter' => array(
                                    'specificationName' => 'integer',
                                    'description' => 'My useful parameter.'
                                )
                            ),
                            $params);
    }
}

class DummyReflectedClass
{
    /**
     * Docblocks will be mocked.
     */
    public function myMethod() {}
    public function getMyField() {}
}
