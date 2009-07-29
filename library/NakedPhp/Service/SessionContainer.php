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
 * @package    NakedPhp_Session
 */

namespace NakedPhp\Service;

/**
 * This class act as a small container for NakedObject instances.
 */
class SessionContainer implements IteratorAggregate
{
    private $_objects = array();

    public function __construct()
    {
    }

    public function add($key, NakedObject $object)
    {
        $this->_objects[$key] = $object;
    }

    public function get($key)
    {
        return $this->_objects;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->_objects);
    }
}
