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

namespace NakedPhp\Reflect\Introspector;
use NakedPhp\Reflect\MethodRemover;
use NakedPhp\Reflect\NameUtils;

/**
 * Operates higher logic level removal operations on an ArrayObject of
 * ReflectionMethod instances.
 */
class ArrayObjectMethodRemover implements MethodRemover
{
    protected $_methods;

    public function __construct(\ArrayObject $methods)
    {
        $this->_methods = $methods;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMethods($prefix)
    {
        $removed = array();
        foreach ($this->_methods->getArrayCopy() as $index => $method) {
            $name = $method->getName();
            if (NameUtils::startsWith($name, $prefix)) {
                $removed[] = $method;
                unset($this->_methods[$index]);
            }
        }
        return $removed;
    }
}
