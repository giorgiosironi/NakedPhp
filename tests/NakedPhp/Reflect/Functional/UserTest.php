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
use NakedPhp\Reflect\ReflectFactory;
use NakedPhp\ProgModel\PhpActionParameter;

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
        $this->assertEquals(array('title' => new PhpActionParameter('string', 'title'),
                                  'text' => new PhpActionParameter('string', 'text')),
                            $sendMessage->getParameters());
        $this->assertNotNull($sendMessage->getFacet('Action\Invocation'));
        $this->assertEquals('void', $sendMessage->getReturnType());

        $deactivate = $actions['deactivate'];
        $this->assertEquals(array(), $deactivate->getParameters());
        $this->assertEquals('boolean', $deactivate->getReturnType());
    }

    public function testGeneratesFieldsFromGetters()
    {
        $fields = $this->_spec->getAssociations();
        $status = $fields['status'];
        $this->assertEquals('string', $status->getType());
    }

    public function testGeneratesStringFieldsFromGettersWithoutAnnotations()
    {
        $fields = $this->_spec->getAssociations();
        $password = $fields['password'];
        $this->assertEquals('string', $password->getType());
    }
}
