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

namespace NakedPhp\Reflect\SpecificationFactory;

class FilesystemClassDiscovererTest extends \PHPUnit_Framework_TestCase
{
    public function testListsAllClassesInAFolder()
    {
        $discoverer = new FilesystemClassDiscoverer(__DIR__ . '/../../Stubs/');
        $services = $discoverer->getList();
        $this->assertContains('UserFactory', $services);
    }

    public function testAcceptsAPrefixForTheClasses()
    {
        $discoverer = new FilesystemClassDiscoverer(__DIR__ . '/../../Stubs/', 'NakedPhp\\Stubs\\');
        $services = $discoverer->getList();
        $this->assertContains('NakedPhp\\Stubs\\UserFactory', $services);
    }
}

