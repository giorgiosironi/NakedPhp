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

class NameUtilsTest extends \PHPUnit_Framework_TestCase
{
    public static function baseNames()
    {
        return array(
            array('getEntity', 'entity'),
            array('setEntity', 'entity')
        );
    }

    /**
     * @dataProvider baseNames
     */
    public function testsFindBaseName($methodName, $baseName)
    {
        $this->assertEquals($baseName, NameUtils::baseName($methodName));
    }

    public static function prefixedNames()
    {
        return array(
            array('getEntity', 'get'),
            array('setEntity', 'set')
        );
    }

    /**
     * @dataProvider prefixedNames
     */
    public function testStartsWith($methodName, $prefix)
    {
        $this->assertTrue(NameUtils::startsWith($methodName, $prefix));
    }

    public static function namesToInflect()
    {
        return array(
            array('entity', 'get', 'getEntity'),
            array('firstName', 'hide', 'hideFirstName')
        );
    }

    /**
     * @dataProvider namesToInflect
     */
    public function testInflectsNamesPrependingAPrefix($name, $prefix, $expected)
    {
        $this->assertEquals($expected, NameUtils::inflectWithPrefix($name, $prefix));
    }
}

