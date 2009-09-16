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
 * @package    NakedPhp_Service
 */

namespace NakedPhp\Service;
use NakedPhp\Metadata\NakedObject;

/**
 * This class act as a small container for NakedObject instances.
 */
class SessionContainer implements \IteratorAggregate
{
    private $_objects = array();
    private $_counter = 0;

    public function __construct()
    {
    }

    public function add(NakedObject $object)
    {
        $index = $this->contains($object);
        if ($index) {
            return $index;
        }
        $this->_counter++;
        $this->_objects[$this->_counter] = $object;
        return $this->_counter;
    }

    public function get($key)
    {
        return $this->_objects[$key];
    }

    public function contains(NakedObject $object)
    {
        foreach ($this->_objects as $index => $o) {
            if ($o === $object) {
                return $index;
            }
        }
        return false;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->_objects);
    }
}
