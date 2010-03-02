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

class FilteredClassDiscovererTest extends \PHPUnit_Framework_TestCase
{
    public function testFiltersOutNotAnnotatedClasses()
    {
        $base = __NAMESPACE__ . '\\';
        $entityClass = $base . 'EntityClass';
        $serviceClass = $base . 'ServiceClass';
        $normalClass = $base . 'NormalClass';
        $discoverer = $this->getMock('NakedPhp\Reflect\SpecificationFactory\ClassDiscoverer');
        $discoverer->expects($this->once())
                   ->method('getList')
                   ->will($this->returnValue(array(
                        $entityClass,
                        $serviceClass,
                        $normalClass
                   )));
        $parser = $this->getMock('NakedPhp\Reflect\DocblockParser');
        $parser->expects($this->any())
               ->method('contains')
               ->will($this->returnCallback(array($this, 'containsAnnotation')));

        $filteredDiscoverer = new FilteredClassDiscoverer($discoverer, $parser);
        $list = $filteredDiscoverer->getList();

        $this->assertContains($entityClass, $list);
        $this->assertContains($serviceClass, $list);
        $this->assertNotContains($normalClass, $list);
    }

    public function containsAnnotation($ann, $docblock)
    {
        return (bool) strstr($docblock, '@' . $ann);
    }
}

/**
 * @Entity
 */
class EntityClass {}

/**
 * @Service
 */
class ServiceClass {}

class NormalClass {}
