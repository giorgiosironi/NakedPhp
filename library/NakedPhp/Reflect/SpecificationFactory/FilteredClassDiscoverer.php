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
use NakedPhp\Reflect\DocblockParser;

/**
 * This class filters out not annotated classes.
 * Decorator for a ClassDiscoverer.
 */
class FilteredClassDiscoverer implements ClassDiscoverer
{
    private $_discoverer;
    private $_parser;

    public function __construct(ClassDiscoverer $discoverer = null, DocblockParser $parser = null)
    {
        $this->_discoverer = $discoverer;
        $this->_parser = $parser;
    }
    
    public function getList()
    {
        $classes = array();
        foreach ($this->_discoverer->getList() as $className) {
            $rc = new \ReflectionClass($className);
            if ($this->_parser->contains('Entity', $rc->getDocComment())
             or $this->_parser->contains('Service', $rc->getDocComment())) {
                $classes[] = $className;
            }
        }
        return $classes;
    }
}
