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

namespace NakedPhp\Reflect\Functional;
use NakedPhp\Reflect\EntityReflector;
use NakedPhp\Reflect\ReflectFactory;
use NakedPhp\ProgModel\PhpActionParameter;

class UserTest extends \PHPUnit_Framework_TestCase
{
    private $_reflector;
    private $_result;

    public function setUp()
    {
        $factory = new ReflectFactory();
        $folder = realpath(__DIR__ . '/../../Stubs/');
        $loader = $factory->createSpecificationLoader($folder,
        'NakedPhp\\Stubs\\');
        $loader->init();
        $this->_spec = $loader->loadSpecification('NakedPhp\Stubs\User');

        // TODO: these setup will be obsolete. When possible, delete it.
        $this->_reflector = $factory->createEntityReflector();
        $this->_result = $this->_reflector->analyze('NakedPhp\Stubs\User');
    }

    public function testFindsOutActions()
    {
        $actions = $this->_spec->getObjectActions();
        $sendMessage = $actions['sendMessage'];
        $this->assertEquals(array('title' => new PhpActionParameter('string', 'title'),
                                  'text' => new PhpActionParameter('string', 'text')),
                            $sendMessage->getParameters());
        $this->assertEquals('void', $sendMessage->getReturnType());
        $deactivate = $actions['deactivate'];
        $this->assertEquals(array(), $deactivate->getParameters());
        $this->assertEquals('boolean', $deactivate->getReturnType());
    }

    // TODO: these tests will be obsolete. When possible, delete them.

    public function testReadsAnnotationsOfMethods()
    {
        $methods = $this->_result->getObjectActions();
        $sendMessage = $methods['sendMessage'];
        $this->assertEquals(array('title' => new PhpActionParameter('string', 'title'),
                                  'text' => new PhpActionParameter('string', 'text')),
                            $sendMessage->getParameters());
        $this->assertEquals('void', $sendMessage->getReturnType());
        $deactivate = $methods['deactivate'];
        $this->assertEquals(array(), $deactivate->getParameters());
        $this->assertEquals('boolean', $deactivate->getReturnType());
    }

    public function testGeneratesFieldsFromGetters()
    {
        $fields = $this->_result->getAssociations();
        $status = $fields['status'];
        $this->assertEquals('string', $status->getType());
    }

    public function testGeneratesStringFieldsFromGettersWithoutAnnotations()
    {
        $fields = $this->_result->getAssociations();
        $status = $fields['password'];
        $this->assertEquals('string', $status->getType());
    }
}
