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
use NakedPhp\MetaModel\NakedObject;
use NakedPhp\ProgModel\NakedBareObject;
use NakedPhp\Reflect\ReflectFactory;
use NakedPhp\Stubs\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    private static $_staticSpec;
    private $_spec;

    public static function setUpBeforeClass()
    {
        $factory = new ReflectFactory();
        $folder = realpath(__DIR__ . '/../../Stubs/');
        $loader = $factory->createSpecificationLoader($folder, 'NakedPhp\\Stubs\\');
        $loader->init();
        self::$_staticSpec = $loader->loadSpecification('NakedPhp\Stubs\User');
    }

    public function setUp()
    {
        $this->_spec = self::$_staticSpec;
    }

    public function testFindsOutActions()
    {
        $actions = $this->_spec->getObjectActions();

        $sendMessage = $actions['sendMessage'];
        $params = $sendMessage->getParameters();
        $this->assertEquals('string', (string) $params['title']->getType());
        $this->assertEquals('string', (string) $params['text']->getType());
        $this->assertEquals('void', (string) $sendMessage->getReturnType());
        $this->assertNotNull($sendMessage->getFacet('Action\Invocation'));
        $invocationFacets = $sendMessage->getFacets('Action\Invocation');
        $this->assertEquals(1, count($invocationFacets));

        $deactivate = $actions['deactivate'];
        $this->assertEquals(array(), $deactivate->getParameters());
        $this->assertEquals('bool', (string) $deactivate->getReturnType());
        $invocationFacet = $deactivate->getFacet('Action\Invocation');
        $result = $invocationFacet->invoke(new NakedBareObject(new User));
        $this->assertTrue($result instanceof NakedObject);
        $this->assertEquals('bool', $result->getSpecification()->getClassName());
    }

    public function testGeneratesFieldsFromGetters()
    {
        $fields = $this->_spec->getAssociations();
        $status = $fields['status'];
        $this->assertEquals('string', (string) $status->getType());
    }

    public function testGeneratesStringFieldsFromGettersWithoutAnnotations()
    {
        $fields = $this->_spec->getAssociations();
        $password = $fields['password'];
        $this->assertEquals('string', (string) $password->getType());
    }
}
